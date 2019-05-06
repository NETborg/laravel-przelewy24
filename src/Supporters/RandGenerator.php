<?php
declare(strict_types=1);

namespace NetborgTeam\P24\Supporters;

class RandGenerator
{
    const SMALL_LETTERS = 1;
    const CAPITAL_LETTERS = 2;
    const NUMBERS_ONLY = 3;
    const LETTERS_ONLY = 4;
    const LETTERS_AND_NUMBERS = 5;


    /**
     * @param  int    $qty
     * @param  int    $type
     * @return string
     */
    public static function generate(int $qty=10, int $type=self::LETTERS_AND_NUMBERS): string
    {
        $lettersSmall = self::getLettersArray1();
        $lettersCapital = self::getLettersArray2();
        $numbers = self::getNumbersArray();
        
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

    /**
     * @param  string $string
     * @return string
     */
    public static function strToHex($string): string
    {
        $s='';
        foreach (str_split($string) as $c) {
            $s.=sprintf("%02X", ord($c));
        }
        return $s;
    }

    /**
     * @param  int    $qty
     * @return string
     */
    public static function generateRandomPassword(int $qty=16): string
    {
        $string = self::generate($qty);
        return self::strToHex($string);
    }

    /**
     * @return int[]
     */
    public static function getLettersArray1(): array
    {
        return [
            97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122
        ];
    }

    /**
     * @return int[]
     */
    public static function getLettersArray2(): array
    {
        return [
            65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90
        ];
    }

    /**
     * @return int[]
     */
    public static function getNumbersArray(): array
    {
        return [
            48, 49, 50, 51, 52, 53, 54, 55, 56, 57
        ];
    }
}
