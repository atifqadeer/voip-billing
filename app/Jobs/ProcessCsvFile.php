<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Cdr;
use App\Models\CompanyServices;
use App\Models\Descriptive;
use App\Models\Setting;
use Hamcrest\Core\Set;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcessCsvFile implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public $filePath;
    protected $providerId;

    public function __construct($filePath, $providerId)
    {
        $this->filePath = $filePath;
        $this->providerId = $providerId;
    }

    public function handle()
    {
        // Get the full file path from storage
        $filePath = storage_path("app/{$this->filePath}");

        // Check if the file exists
        if (!file_exists($filePath)) {
            Log::error("CSV file not found: {$filePath}");
            return;
        }

        // Open the file in read mode
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            Log::error("Unable to open file: {$filePath}");
            return;
        }

        // Read the header row
        $header = fgetcsv($handle);
        if ($header === false) {
            Log::error("CSV header not found in file: {$filePath}");
            fclose($handle);
            return;
        }

        // Convert headers to lowercase for consistency
        $header = array_map('strtolower', $header);

        // Fetch UK Mobile Rate directly
        $ukMobileRate = CompanyServices::join('descriptive', 'company_services.descriptive_id', '=', 'descriptive.id')
            ->where('service_id', 1)
            ->where('description_name', 'like', 'UK - Mobile%') // Match "UK - Mobile"
            ->value('sell_rate');

        // Fetch UK General Rate directly
        $ukGeneralRate = CompanyServices::join('descriptive', 'company_services.descriptive_id', '=', 'descriptive.id')
            ->where('service_id', 1)
            ->where('description_name', 'like', 'UK -%') // Match any "UK -"
            ->where('description_name', 'not like', 'UK - Mobile%') // Exclude "UK - Mobile"
            ->value('sell_rate');

        // Fetch Non-UK Rate directly
        $non_UK = CompanyServices::join('descriptive', 'company_services.descriptive_id', '=', 'descriptive.id')
            ->where('service_id', 1)
            ->where('description_name', 'not like', 'UK -%') // Exclude all "UK -"
            ->value('sell_rate');

        Log::info("UK Mobile Rate: {$ukMobileRate}, UK General Rate: {$ukGeneralRate}, Non-UK Rate: {$non_UK}");

        $extraCharge = Setting::where('param', 'extra_charges_on_cdr')->first()['value'];

        // Cache descriptive data and company services to avoid repeated queries
        $descriptives = Descriptive::where('status', 'enable')
            ->get()->pluck('replace_with', 'description_name');
        Log::info("Descriptives: " . json_encode($descriptives));

        // Step 1: Collect unique identifiers based on from_cli, to_number, and date
        $existingIdentifiers = Cdr::select('from_cli', 'to_number', 'date')
            ->get()
            ->map(function ($cdr) {
                // Combine fields to form a unique identifier, including the date
                return $cdr->from_cli . '-' . $cdr->to_number . '-' . $cdr->date;
            })
            ->flip() // Flip the array to use as a set for fast lookups
            ->toArray();

        // Initialize counters and bulk data array
        $rejected = 0;
        $success = 0;
        $bulkData = [];

        // Step 2: Process each row from the CSV
        while (($filedata = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $data = [];

            // Map the CSV data to the header fields
            foreach ($header as $key => $column) {
                $data[$column] = isset($filedata[$key]) ? utf8_encode($filedata[$key]) : '';
            }

            // Skip rows with 'to (descriptive)' = 'NTS to VoIP'
            if ($data['to (descriptive)'] == 'NTS to VoIP') {
                $rejected++;
                continue;
            }

            // Step 3: Check if the current record already exists using the combined identifier
            $identifier = $data['from (cli)'] . '-' . $data['to (number)'] . '-' . Carbon::parse($data['date'])->format('Y-m-d'); // Combine fields with date

            // Check if the identifier already exists in the set
            if (isset($existingIdentifiers[$identifier])) {
                $rejected++;
                continue;
            }

            // Step 4: Simplify the descriptive plan
            $simplified_descriptive = $this->simplifyDescriptive($data['to (descriptive)'], $descriptives);

            // Step 5: Calculate the price for the record
            $price = $this->calculateValue(
                $simplified_descriptive,
                $data['duration (seconds) of call'],
                $data['peak rate'],
                $data['connection rate'],
                $ukMobileRate,
                $ukGeneralRate,
                $non_UK,
                $extraCharge
            );

            // Format the duration into a more readable time format
            $duration_time = $this->secondsToTimeFormat($data['duration (seconds) of call']);

            // Prepare data for bulk insert
            $bulkData[] = [
                'reference' => $data['reference'],
                'provider_id' => $this->providerId,
                'trunk' => $data['trunk'],
                'tag' => $data['tag'],
                'date' => Carbon::parse($data['date'])->format('Y-m-d'),
                'time' => $data['time'],
                'from_cli' => $data['from (cli)'],
                'from_descriptive' => $data['from (descriptive)'],
                'to_number' => $data['to (number)'],
                'to_descriptive' => $data['to (descriptive)'],
                'destination_id' => $data['destination id'],
                'duration_seconds' => $data['duration (seconds) of call'],
                'billable_duration_seconds' => $data['billable duration (seconds)'],
                'peak_duration' => $data['peak duration'],
                'off_peak_duration' => $data['offpeak duration'],
                'weekend_duration' => $data['weekend duration'],
                'peak_rate' => $data['peak rate'],
                'off_peak_rate' => $data['offpeak rate'],
                'weekend_rate' => $data['weekend rate'],
                'connection_rate' => $data['connection rate'],
                'total_charge' => $data['total charge'],
                'currency' => $data['currency'],
                'simplified_to_descriptive' => $simplified_descriptive,
                'bill_amount' => $price,
                'calculated_duration' => $duration_time,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            // Insert in bulk when the batch reaches 1000 records
            if (count($bulkData) >= 1000) {
                DB::table('cdrs')->insert($bulkData);
                $success += count($bulkData);
                $bulkData = []; // Reset the array after inserting
            }
        }

        // Step 6: Insert any remaining records
        if (count($bulkData) > 0) {
            DB::table('cdrs')->insert($bulkData);
            $success += count($bulkData);
        }

        fclose($handle);

        // Log the results
        Log::info("Processed CSV file: {$filePath}, Success: {$success}, Rejected: {$rejected}");
    }

    // private function simplifyDescriptive($raw_descriptives, $descriptives)
    // {
    //     $raw_descriptives = strtolower(trim($raw_descriptives));
    //     Log::info("Raw Descriptive: {$raw_descriptives}");
    //     foreach ($descriptives as $descriptionName => $replaceWith) {
    //         $replaceArray = explode(',', $replaceWith);
    //         $replaceArray = array_map('strtolower', array_map('trim', $replaceArray));
    //         Log::info("Replace Array: " . json_encode($replaceArray));
    //         if (in_array($raw_descriptives, $replaceArray)) {
    //             return $descriptionName;
    //         }
    //     }
    //     return '';
    // }

    private function simplifyDescriptive($raw_descriptives, $descriptives)
    {
        $raw = strtolower(trim($raw_descriptives));
        Log::info("Raw Descriptive: {$raw}");

        // 1️⃣ Loop through existing descriptive lists
        foreach ($descriptives as $descriptionName => $replaceWith) {

            $replaceArray = explode(',', $replaceWith);
            $replaceArray = array_map('strtolower', array_map('trim', $replaceArray));

            Log::info("Replace Array: " . json_encode($replaceArray));

            // ✔ MATCH FOUND
            if (in_array($raw, $replaceArray)) {
                return $descriptionName;
            }
        }

        // 2️⃣ NO MATCH FOUND → Save once
        Log::info("No match found. Saving new descriptive: {$raw}");

        Descriptive::firstOrCreate(
            ['description_name' => $raw_descriptives],   // unique key
            ['replace_with' => '']
        );


        return $raw;
    }

    // private function calculateValue($descriptive, $callDuration, $peakRate, $connectionCharges, $ukMobileRate, $ukGeneralRate, $non_UK, $extraCharge)
    // {
    //     $minutes = (float) $callDuration;
    //     $rate = (float) $peakRate;
    //     $connectionCharges = (float) $connectionCharges;

    //    // Initialize the result variable
    //     $result = 0;

    //     // Check the conditions based on the description (J11683)
    //     if (strpos($descriptive, "UK - Mobile") !== false) {
    //         // UK Mobile rate
    //         $result = ceil($minutes / 60) * $ukMobileRate;
    //     } elseif (strpos($descriptive, "UK - ") !== false && $rate < 0.016 && $rate > 0) {
    //         // UK General rate with conditions
    //         $result = ceil($minutes / 60) * $ukGeneralRate;
    //     } elseif ($rate > 0) {
    //         // Non-UK rate with additional conditions
    //         $result = ceil($minutes / 60) * $rate + ((ceil($minutes / 60) * $rate) * $extraCharge);

    //         // Check additional conditions for UK rates and apply extra adjustments
    //         if (strpos($descriptive, "UK - ") === false) {
    //             $result += $non_UK;
    //         }
    //     }

    //     // Add the additional amount (U11683)
    //     $result += $connectionCharges;

    //     // Round the result to two decimal places
    //     $result = round($result, 2);

    //     // Return the final result
    //     return $result;
    // }

    private function calculateValue($descriptive, $callDuration, $peakRate, $connectionCharges, $ukMobileRate, $ukGeneralRate, $non_UK, $extraCharge)
    {
        $minutes = (float) $callDuration;
        $rate = (float) $peakRate;
        $connectionCharges = (float) $connectionCharges;

        // Initialize the result variable
        $result = 0;

        // Check if the descriptive matches exactly "UK - Mobile" (excluding additional terms)
        if (preg_match("/^UK - Mobile\b/i", $descriptive)) {
            // UK Mobile rate (using ceil to round up as Excel does)
            $result = ceil($minutes / 60) * $ukMobileRate;
        }
        // Check if "UK - " is found in $descriptive and "UK - Mobile" is NOT part of the descriptive, using preg_match
        elseif (preg_match("/^UK - (?!Mobile\b)/i", $descriptive) && $rate < 0.016 && $rate > 0) {
            // UK General rate with the given condition, but only if it's not "UK - Mobile"
            $result = ceil($minutes / 60) * $ukGeneralRate;
        }
        // Check if $rate is greater than 0 for non-UK calculations
        elseif ($rate > 0) {
            // Non-UK rate calculation with additional conditions
            $roundedMinutes = ceil($minutes / 60);
            $base = $roundedMinutes * $rate;

            // Apply extra charge (30%)
            $additional = $base * 0.30;

            // Check if it's non-UK, apply additional charges
            if (!preg_match("/^UK - /", $descriptive)) {  // If it doesn't contain "UK - "
                $additional += $non_UK;
            }

            $result = round($base + $additional + 0.005, 2);
        } else {
            $result = 0;
        }

        // Add connection charges
        $result += $connectionCharges;

        // Round to two decimal places (to match Excel formula)
        $result = round($result, 2);

        return $result;
    }

    function secondsToTimeFormat($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
    }
}
