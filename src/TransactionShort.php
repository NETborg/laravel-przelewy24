<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 14:05
 */

namespace NetborgTeam\P24;

use Illuminate\Support\Carbon;

/**
 * Class TransactionShort
 * @package NetborgTeam\P24
 *
 * @property int $orderId
 * @property string $sessionId
 * @property int $status
 * @property int $amount
 * @property string $currency
 * @property string|Carbon $date
 * @property string|Carbon $dateOfTransaction
 * @property string $clientEmail
 * @property string $accountMD5
 * @property int $paymentMethod
 * @property string $description
 * @property string $clientAddress
 * @property string $clientCity
 * @property string $clientName
 * @property string $clientPostcode
 * @property int $batchId
 * @property int $fee
 * @property string $statement
 */
class TransactionShort extends P24ResponseObject
{
    /**
     * @var String[]
     */
    protected $keys = [
        'orderId',
        'sessionId',
        'status',
        'amount',
        'currency',
        'date',
        'dateOfTransaction',
        'clientEmail',
        'accountMD5',
        'paymentMethod',
        'description',
        'clientAddress',
        'clientCity',
        'clientName',
        'clientPostcode',
        'batchId',
        'fee',
        'statement',
    ];

    /**
     * @var String[]
     */
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
