<?php
namespace NetborgTeam\P24\Supporters;

/**
 * Description of NumberFormatter
 *
 * @author Roland Kolodziej <roland@netborg-software.com>
 */
class NumberFormatter
{
    public static $N_TO_DAYOFWEEK = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wendsday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday',
    ];
    
    public static $DAYOFWEEK_TO_N = [
        'Monday' => 1,
        'Tuesday' => 2,
        'Wendsday' => 3,
        'Thursday' => 4,
        'Friday' => 5,
        'Saturday' => 6,
        'Sunday' => 7,
    ];


    public static function fitFraction($number, $precision=5)
    {
        $number = str_replace([',', ' '], ['.', ''], $number);
        if (strpos($number, '.') !== false) {
            list($decimal, $fraction) = explode('.', $number);
        } else {
            $decimal = $number;
            $fraction = '';
        }
        
        $len = strlen($fraction);
        if ($len > $precision) {
            $fitted = round(floatval($number), $precision);
        } elseif ($len < $precision) {
            $fitted = $decimal.'.'.str_pad($fraction, $precision, '0');
        } else {
            $fitted = $number;
        }
        
        return $fitted;
    }
}
