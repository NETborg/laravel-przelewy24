<?php
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 14:05
 */

namespace NetborgTeam\P24;

use Carbon\Carbon;

class TransactionShort extends P24ResponseObject
{
    protected $keys = [
        'orderId',
        'orderIdFull',
        'sessionId',
        'status',
        'amount',
        'date',
        'dateOfTransaction',
        'clientEmail',
        'accountMD5',
        'paymentMethod',
        'description'
    ];

    protected $dates = ['date', 'dateOfTransaction'];


    public function __get($key)
    {
        if (in_array($key, $this->keys)) {
            if (in_array($key, $this->dates) && !empty($this->data[$key])) {
                return Carbon::createFromFormat('YmdHi', $this->data[$key]);
            }
            return $this->data[$key];
        }
        return null;
    }
}
