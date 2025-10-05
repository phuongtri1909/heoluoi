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

    /**
     * Tính cám tặng theo công thức hàm mũ
     * 
     * @param int $amountAfterFee Số tiền sau phí
     * @param int $bonusBaseAmount Mốc cơ bản (100000)
     * @param int $bonusBaseCam Cám tặng mốc cơ bản (300)
     * @param int $bonusDoubleAmount Mốc gấp đôi (300000)
     * @param int $bonusDoubleCam Cám tặng mốc gấp đôi (1000)
     * @return int Số cám tặng
     */
    function calculateBonusCoins($amountAfterFee, $bonusBaseAmount, $bonusBaseCam, $bonusDoubleAmount, $bonusDoubleCam)
    {
        if ($amountAfterFee < $bonusBaseAmount) {
            return 0;
        }

        // Tính số mũ b
        // b = log(300000/100000)(1000/300) = log3(3.333...) ≈ 1.096
        $ratioAmount = $bonusDoubleAmount / $bonusBaseAmount; // 300000/100000 = 3
        $ratioBonus = $bonusDoubleCam / $bonusBaseCam; // 1000/300 = 3.333...
        $b = log($ratioBonus) / log($ratioAmount); // ≈ 1.096

        // Tính hệ số a
        // a = 300/(100000)^b
        $a = $bonusBaseCam / pow($bonusBaseAmount, $b);

        // Tính bonus theo công thức: bonus = a * (amountAfterFee)^b
        return (int) floor($a * pow($amountAfterFee, $b));
    }

    /**
     * Tính tổng cám nhận được (cám cộng + cám tặng)
     * 
     * @param int $amount Số tiền nạp
     * @param int $coinExchangeRate Tỷ giá (100)
     * @param int $coinPercent Phí giao dịch (%)
     * @param int $bonusBaseAmount Mốc cơ bản bonus
     * @param int $bonusBaseCam Cám tặng mốc cơ bản
     * @param int $bonusDoubleAmount Mốc gấp đôi bonus
     * @param int $bonusDoubleCam Cám tặng mốc gấp đôi
     * @return array ['base_coins' => int, 'bonus_coins' => int, 'total_coins' => int]
     */
    function calculateTotalCoins($amount, $coinExchangeRate, $coinPercent, $bonusBaseAmount, $bonusBaseCam, $bonusDoubleAmount, $bonusDoubleCam)
    {
        // Tính cám cơ bản
        $feeAmount = ($amount * $coinPercent) / 100;
        $amountAfterFee = $amount - $feeAmount;
        $baseCoins = floor($amountAfterFee / $coinExchangeRate);

        // Tính cám tặng
        $bonusCoins = calculateBonusCoins($amountAfterFee, $bonusBaseAmount, $bonusBaseCam, $bonusDoubleAmount, $bonusDoubleCam);

        return [
            'base_coins' => (int) $baseCoins,
            'bonus_coins' => (int) $bonusCoins,
            'total_coins' => (int) ($baseCoins + $bonusCoins)
        ];
    }
}
