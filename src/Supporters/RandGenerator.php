<?php

/*
 * Random characters generator
 */

namespace NetborgTeam\P24\Supporters;

/**
 * Description of RandGenerator
 *
 * @author Roland Kolodziej <roland@netborg-software.com>
 */
class RandGenerator
{
    public static function generate($qty=10, $type=5)
    {
        $lettersSmall = self::getLettersArray1();
        $lettersCapital = self::getLettersArray2();
        $numbers = self::getNumbersArray();
        
        $scope = [];
        
        if (is_string($type)) {
            if (in_array($type, ["small-letters", "s-l", "sl"])) {
                $type = 1;
            } elseif (in_array($type, ["capital-letters", "c-l", "cl"])) {
                $type = 2;
            } elseif (in_array($type, ["numbers","n"])) {
                $type = 3;
            } elseif (in_array($type, ["letters", "all-letters", "just-letters", "only-letters", "l"])) {
                $type = 4;
            } else {
                $type = 5;
            }
        }
        
        switch ($type) {
            case 1:
                $scope = $lettersSmall; break;
            case 2:
                $scope = $lettersCapital; break;
            case 3:
                $scope = $numbers; break;
            case 4:
                $scope = array_merge($lettersSmall, $lettersCapital); break;
            case 5:
                $scope = array_merge($lettersSmall, $lettersCapital, $numbers); break;
            default:
                $scope = array_merge($lettersSmall, $lettersCapital, $numbers); break;
        }
        
        $rands = [];
        
        for ($i=0; $i<$qty; $i++) {
            $rand = rand(0, count($scope)-1);
            $rands[] = chr($scope[$rand]);
        }
        
        return implode('', $rands);
    }
    
    public static function strToHex($string)
    {
        $s='';
        foreach (str_split($string) as $c) {
            $s.=sprintf("%02X", ord($c));
        }
        return($s);
    }
    
    public static function generateRandomPassword($qty=16)
    {
        $string = self::generate($qty);
        return self::strToHex($string);
    }
    
    public static function getLettersArray1()
    {
        return [
            97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122,];
    }
    
    public static function getLettersArray2()
    {
        return [
            65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90
        ];
    }
    
    public static function getNumbersArray()
    {
        return [
            48, 49, 50, 51, 52, 53, 54, 55, 56, 57
        ];
    }
}
