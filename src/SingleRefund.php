<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 15:07
 */

namespace NetborgTeam\P24;

/**
 * Class SingleRefund
 * @package NetborgTeam\P24
 *
 * @property int $orderId
 * @property string $sessionId
 * @property int $amount
 * @property int status
 * @property string|null $error
 */
class SingleRefund extends P24ResponseObject
{
    /**
     * @var String[]
     */
    protected $keys = [
        'orderId',
        'sessionId',
        'amount',
        'status',
        'error',
    ];
}
