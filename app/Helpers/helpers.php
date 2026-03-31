<?php

if (!function_exists('durationToSeconds')) {
    // Function to convert HH:MM:SS to seconds
    function durationToSeconds($duration) {
        list($hours, $minutes, $seconds) = explode(':', $duration);
        return $hours * 3600 + $minutes * 60 + $seconds;
    }
}

if (!function_exists('secondsToDuration')) {
    // Function to convert seconds to HH:MM:SS
    function secondsToDuration($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}

if (! function_exists('getSetting')) {
    function getSetting($key, $default = null)
    {
        // Retrieve the setting from the database if not shared
        static $settings = null;

        if ($settings === null) {
            $settings = \App\Models\Setting::pluck('value', 'param')->toArray();
        }

        return $settings[$key] ?? $default;
    }
}
