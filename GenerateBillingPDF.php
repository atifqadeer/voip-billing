<?php

namespace App\Actions;

use App\Models\Billing;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use TCPDF;

class GenerateBillingPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Check if the directory exists, if not, create it
            $billsDirectory = public_path('bills');
            if (!is_dir($billsDirectory)) {
                if (!mkdir($billsDirectory, 0755, true)) {
                    Log::error("Failed to create the 'bills' directory.");
                    throw new \Exception("Failed to create the 'bills' directory.");
                }
            }
    
            // Check if the directory is writable
            if (!is_writable($billsDirectory)) {
                Log::error("The 'bills' directory is not writable.");
                throw new \Exception("The 'bills' directory is not writable.");
            }
    
            // Fetch the billing instance to generate the PDF
            $billing = Billing::findOrFail($this->data['billing']->id);
            
            // Assuming the client name is stored in the Billing model, or a related Client model.
            $clientName = $billing->client->client_name; // Adjust this if needed to fetch the client name
    
            // Initialize TCPDF
            $pdf = new TCPDF();
    
            // Set up basic document info (optional, but recommended)
            $pdf->SetCreator('TCPDF');
            $pdf->SetAuthor('IBSTEC');
            $pdf->SetTitle('Billing PDF');
            
            // Set font (adjust size and type as per your requirement)
            $pdf->SetFont('helvetica', '', 8);
            $pdf->AddPage();  // You need to explicitly add a page
    
            // Prepare your HTML content (Blade rendering)
            $html = view('admin.billings.pdf', $this->data)->render(); // Render Blade view into HTML
    
            // Output the HTML content to the PDF, using TCPDF's writeHTML function
            $pdf->writeHTML($html, true, false, true, false, '');
    
            // Create a valid file name with the client name (sanitize it if necessary)
            $sanitizedClientName = preg_replace('/[^a-zA-Z0-9-_ ]/', '', $clientName); // Removes special chars
            $sanitizedClientName = str_replace(' ', '_', $sanitizedClientName); // Replace spaces with underscores

            $pdfName = 'bill_of_' . $billing->month . '_' . $billing->year . '_' . $sanitizedClientName . '_' . $billing->id . '.pdf';
    
            // Save the PDF to the desired location
            $pdfPath = public_path('bills/' . $pdfName);
            $pdf->Output($pdfPath, 'F');  // Save as file
    
            // Update the Billing record with the generated PDF file name
            $billing->update(['pdf_file_name' => $pdfName]);
    
            Log::info("PDF generated and saved successfully for Billing ID: {$billing->id}, Client: {$clientName}");
    
        } catch (\Exception $e) {
            Log::error("Failed to generate PDF for Billing ID: {$this->data['billing']->id}. Error: " . $e->getMessage());
            Log::error("Stack Trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Generate a PDF file name based on billing details.
     *
     * @param \App\Models\Billing $billing
     * @return string
     */
    protected function generatePdfFileName(Billing $billing): string
    {
        // Generate a file name based on the billing details (month, year, client name)
        return sprintf(
            'bill_%s_%s_%s.pdf',
            $billing->month,
            $billing->year,
            $billing->client->client_name
        );
    }
}