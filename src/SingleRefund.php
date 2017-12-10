<?php
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 15:07
 */

namespace NetborgTeam\P24;


class SingleRefund extends P24ResponseObject
{

    protected $keys = [
        'orderId',
        'sessionId',
        'amount',
        'status',
        'error',
    ];

}