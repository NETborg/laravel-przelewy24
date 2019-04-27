<?php
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 14:03
 */

namespace NetborgTeam\P24;

class TransactionFullResult extends P24Response
{
    public function parseResult($result)
    {
        if (is_object($result)) {
            return new TransactionFull($result);
        }

        return null;
    }
}
