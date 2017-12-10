<?php
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 15:09
 */

namespace NetborgTeam\P24\Supporters;


class StatusTranslator
{
    public static function translate($statusId)
    {
        switch($statusId) {
            case 0 : $status = "not paid"; break;
            case 1 : $status = "prepayment"; break;
            case 2 : $status = "completed"; break;
            case 3 : $status = "refunded"; break;
            default: $status = "unknown"; break;
        }

        return $status;
    }
}