<?php

namespace App;

use Carbon\CarbonPeriod;
use DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class Helper
{
    public function test()
    {
        return get_called_class();
    }

    public static function convertNumberToWords($number)
    {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'fourty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
            1000000000000 => 'trillion',
            1000000000000000 => 'quadrillion',
            1000000000000000000 => 'quintillion',
        ];

        if (! is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -'.PHP_INT_MAX.' and '.PHP_INT_MAX,
                E_USER_WARNING
            );

            return false;
        }

        if ($number < 0) {
            return $negative.self::convertNumberToWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            [$number, $fraction] = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen.$dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds].' '.$dictionary[100];
                if ($remainder) {
                    $string .= $conjunction.self::convertNumberToWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::convertNumberToWords($numBaseUnits).' '.$dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::convertNumberToWords($remainder);
                }
                break;
        }

        if ($fraction !== null && is_numeric($fraction)) {
            $string .= $decimal;
            $words = [];
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return ucwords($string);
    }

    public static function currencyToWords($num)
    {
        $decones = [
            '1' => 'One',
            '01' => 'One',
            '2' => 'Two',
            '02' => 'Two',
            '3' => 'Three',
            '03' => 'Three',
            '4' => 'Four',
            '04' => 'Four',
            '5' => 'Five',
            '05' => 'Five',
            '6' => 'Six',
            '06' => 'Six',
            '7' => 'Seven',
            '07' => 'Seven',
            '8' => 'Eight',
            '08' => 'Eight',
            '9' => 'Nine',
            '09' => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
        ];
        $ones = [
            0 => ' ',
            '00' => ' ',
            '000' => ' ',
            '1' => 'One',
            '01' => 'One',
            '001' => 'One',
            '2' => 'Two',
            '02' => 'Two',
            '002' => 'Two',
            '3' => 'Three',
            '003' => 'Three',
            '4' => 'Four',
            '04' => 'Four',
            '004' => 'Four',
            '5' => 'Five',
            '05' => 'Five',
            '005' => 'Five',
            '6' => 'Six',
            '06' => 'Six',
            '006' => 'Six',
            '7' => 'Seven',
            '07' => 'Seven',
            '007' => 'Seven',
            '8' => 'Eight',
            '08' => 'Eight',
            '008' => 'Eight',
            '9' => 'Nine',
            '09' => 'Nine',
            '009' => 'Nine',
            10 => 'Ten',
            '010' => 'Ten',
            11 => 'Eleven',
            '011' => 'Eleven',
            12 => 'Twelve',
            '012' => 'Twelve',
            13 => 'Thirteen',
            '013' => 'Thirteen',
            14 => 'Fourteen',
            '014' => 'Fourteen',
            15 => 'Fifteen',
            '015' => 'Fifteen',
            16 => 'Sixteen',
            '016' => 'Sixteen',
            17 => 'Seventeen',
            '017' => 'Seventeen',
            18 => 'Eighteen',
            '018' => 'Eighteen',
            19 => 'Nineteen',
            '019' => 'Nineteen',
        ];
        $tens = [
            0 => '',
            1 => '',
            2 => 'Twenty',
            3 => 'Thirty',
            4 => 'Forty',
            5 => 'Fifty',
            6 => 'Sixty',
            7 => 'Seventy',
            8 => 'Eighty',
            9 => 'Ninety',
        ];
        $hundreds = [
            'Hundred',
            'Thousand',
            'Million',
            'Billion',
            'Trillion',
            'Quadrillion',
        ]; // limit t quadrillion
        $num = number_format($num, 2, '.', ',');
        $num_arr = explode('.', $num);
        $wholenum = $num_arr[0];
        $decnum = $num_arr[1];
        $whole_arr = array_reverse(explode(',', $wholenum));
        krsort($whole_arr);
        $rettxt = '';
        foreach ($whole_arr as $key => $i) {
            if ($i < 20) {
                $rettxt .= $ones[$i];
            } elseif ($i < 100) {
                $rettxt .= $tens[substr($i, 0, 1)];
                $rettxt .= ' '.$ones[substr($i, 1, 1)];
            } else {
                $rettxt .= $ones[substr($i, 0, 1)].' '.$hundreds[0];
                if (substr($i, 1, 1) >= 2) {
                    $rettxt .= ' '.$tens[substr($i, 1, 1)];
                    $rettxt .= ' '.$ones[substr($i, 2, 1)];
                } else {
                    $rettxt .= ' '.$ones[substr($i, 1, 2)];
                }
            }
            if ($key > 0) {
                $rettxt .= ' '.$hundreds[$key].' ';
            }
        }
        $rettxt = $rettxt.' Rupees';

        if ($decnum > 0) {
            $rettxt .= ' and ';
            if ($decnum < 20) {
                $rettxt .= $decones[$decnum];
            } elseif ($decnum < 100) {
                $rettxt .= $tens[substr($decnum, 0, 1)];
                $rettxt .= ' '.$ones[substr($decnum, 1, 1)];
            }
            $rettxt = $rettxt.' Paisa';
        }

        return ucwords($rettxt.' Only');
    }

    public static function convertCurrencyToWords(float $number)
    {
        $prefix = '';
        if ($number < 0) {
            $prefix = 'Negative ';
            $number = abs($number);
        }
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = [];
        $words = [0 => '', 1 => 'one', 2 => 'two',
            3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
            7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve',
            13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
            16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
            19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
            40 => 'forty', 50 => 'fifty', 60 => 'sixty',
            70 => 'seventy', 80 => 'eighty', 90 => 'ninety'];
        $digits = ['', 'hundred', 'thousand', 'lakh', 'crore'];
        while ($i < $digits_length) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? '' : null;
                $str[] = ($number < 21) ? $words[$number].' '.$digits[$counter].$plural.' '.$hundred : $words[floor($number / 10) * 10].' '.$words[$number % 10].' '.$digits[$counter].$plural.' '.$hundred;
            } else {
                $str[] = null;
            }
        }
        $Rupees = implode('', array_reverse($str));
        $paisa = ($decimal > 0) ? 'and '.($words[$decimal / 10].' '.$words[$decimal % 10]).' Paisa' : '';
        $words = ($Rupees ? $Rupees.'Rupees ' : '').$paisa;

        return $prefix.ucwords(preg_replace('/\s+/', ' ', $words.' Only'));
    }

    public static function getMonthArray()
    {
        return array_reduce(range(1, 12), function ($rslt, $m) {
            $rslt[$m] = date('F', mktime(0, 0, 0, $m, 10));

            return $rslt;
        });
    }

    public static function getSpecificDays($startDate, $endDate, $weekdayNumber)
    {
        $startDt = strtotime($startDate);
        $endDt = strtotime($endDate);
        $dateSun = [];
        do {
            if (date('w', $startDt) != $weekdayNumber) {
                $startDt += (24 * 3600); // add 1 day
            }
        } while (date('w', $startDt) != $weekdayNumber);

        while ($startDt <= $endDt) {
            $dateSun[] = date('Y-m-d', $startDt);
            $startDt += (7 * 24 * 3600); // add 7 days
        }

        return $dateSun;
    }

    public static function getIntervalInHour($interval)
    {
        $hour = $interval->format('%h');
        $minute = $interval->format('%i');
        $result = $hour.'.'.$minute;

        return floatval($result);
    }

    public static function getLeaveAbbreviation($leaveType, $leaveMode)
    {
        if ($leaveType == 3) {               // Sick leave
            if ($leaveMode == 3) {          // Full day
                return 'S';
            } elseif ($leaveMode == 6) {    // First half
                return 'S½';
            } elseif ($leaveMode == 9) {    // Second half
                return 'S½';
            } elseif ($leaveMode == 12) {    // Second half
                return 'SL¼';
            } else {
                return 'S';
            }
        } elseif ($leaveType == 6) {        // Annual leave
            if ($leaveMode == 3) {          // Full day
                return 'A';
            } elseif ($leaveMode == 6) {    // First half
                return 'A½';
            } elseif ($leaveMode == 9) {    // Second half
                return 'A½';
            } elseif ($leaveMode == 12) {   // 2 hours
                return 'A¼';
            } else {
                return 'A';
            }
        } elseif ($leaveType == 9) {        // Maternity leave
            return 'ML';
        } elseif ($leaveType == 12) {       // Flexible leave
            return 'FL';
        } elseif ($leaveType == 18) {       // Mourning leave
            return 'BL';
        } elseif ($leaveType == 21) {       // Special vacation leave
            return 'SVL';
        } elseif ($leaveType == 24) {       // Unpaid Leave
            return 'NPL';
        } elseif ($leaveType == 27) {       // Paternity leave
            return 'PL';
        } else {                            // Just Leave
            return 'NPL';
        }
    }

    public static function isGreaterThanCurrentDate($date)
    {
        $givenDate = new DateTime($date);
        $currentDate = new DateTime;

        return $givenDate > $currentDate;
    }

    public static function getDatesBetween($startDate, $endDate)
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $key => $value) {
            array_push($dates, $value->format('Y-m-d'));
        }

        return $dates;
    }

    public static function convertTime($decimal)
    {
        $time = '';

        if ($decimal) {
            $time = Date::excelToDateTimeObject($decimal)->format('Y-m-d H:i:s');
            $time = explode(' ', $time)[1];
            [$hour, $minute, $second] = explode(':', $time);
            $time = $hour.':'.$minute;
        }

        return $time;
    }

    public static function getMonthNumber($monthName)
    {
        $months = [
            'january',
            'february',
            'march',
            'april',
            'may',
            'june',
            'july',
            'august',
            'september',
            'october',
            'november',
            'december',
        ];

        $monthNumber = '';

        if (in_array(trim(strtolower($monthName)), $months)) {
            $monthNumber = array_search(trim(strtolower($monthName)), $months);
            $monthNumber += 1;
        }

        return $monthNumber;
    }

    public static function tdsPercentages()
    {
        return [
            0,
            1,
            1.5,
            2.5,
            10,
            15,
            25,
        ];
    }

    public static function convertToMinutes($hourMinute)
    {
        $hours = floor($hourMinute);
        $minutes = round(($hourMinute - $hours) * 100);

        return ($hours * 60) + $minutes;
    }

    public static function convertToHourMinute($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return round(floatval($hours + ($mins / 100)), 2);
    }

    public function getHourDiff($startHours, $endHours)
    {
        $diffMinutes = abs($this->convertToMinutes($endHours) - $this->convertToMinutes($startHours));

        return $this->convertToHourMinute($diffMinutes);
    }
}
