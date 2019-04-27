<?php
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 14:59
 */

namespace NetborgTeam\P24;

class TransactionRefundResult extends P24Response
{
    public function parseResult($result)
    {
        $refunds = [];
        if (is_array($result)) {
            foreach ($result as $r) {
                $refunds[] = new SingleRefund($r);
            }
        } elseif (is_object($result)) {
            $refunds[] = new SingleRefund($result);
        }

        return $refunds;
    }
}
