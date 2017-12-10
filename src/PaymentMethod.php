<?php
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 13:46
 */

namespace NetborgTeam\P24;


class PaymentMethod extends P24ResponseObject
{

    protected $keys = [
        'id',
        'name',
        'status'
    ];

}