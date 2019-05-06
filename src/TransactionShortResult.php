<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 14:03
 */

namespace NetborgTeam\P24;

class TransactionShortResult extends P24Response
{
    /**
     * @param $result
     * @return TransactionShort|null
     */
    public function parseResult($result): ?TransactionShort
    {
        if (is_object($result)) {
            return new TransactionShort($result);
        }

        return null;
    }
}
