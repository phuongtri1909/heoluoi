<?php

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;

if (!function_exists('format_number_short')) {
    /**
     * Formats a number into a short human-readable format (k, M, B).
     *
     * @param int|float $number The number to format.
     * @param int $precision The number of decimal places.
     * @return string The formatted number.
     */
    function format_number_short($number, int $precision = 1): string
    {
        if ($number < 1000) {
            return number_format($number);
        }

        $suffixes = ['', 'k', 'M', 'B', 'T'];
        $power = floor(log10($number) / 3);

        $power = min($power, count($suffixes) - 1);

        $divisor = pow(1000, $power);
        $formattedNumber = $number / $divisor;

        $formatted = number_format($formattedNumber, $precision);

        if ($precision === 1 && str_ends_with($formatted, '.0')) {
            $formatted = substr($formatted, 0, -2);
        }

        return $formatted . $suffixes[$power];
    }

    function time_elapsed_string($dateTime, bool $short = false, ?string $locale = null): string
    {
        if (is_null($dateTime)) {
            return '';
        }

        try {
            $carbonDate = ($dateTime instanceof Carbon) ? $dateTime : Carbon::parse($dateTime);
        } catch (\Exception $e) {
            return '';
        }

        $currentLocale = $locale ?? App::getLocale();

        return $carbonDate->locale($currentLocale)->diffForHumans([
            'short' => $short,
            'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
            'options' => Carbon::NO_ZERO_DIFF,
        ]);
    }

    function cleanDescription($content, $limit = 150)
    {
        $text = strip_tags($content);

        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        $text = preg_replace('/\s+/', ' ', $text);

        $text = trim($text);

        return Str::limit($text, $limit);
    }

    function generateRandomOTP($length = 6)
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= rand(0, 9);
        }
        return $otp;
    }
}
