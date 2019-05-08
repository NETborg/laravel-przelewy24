<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 13:46
 */

namespace NetborgTeam\P24;

/**
 * Class PaymentMethod
 * @package NetborgTeam\P24
 *
 * @property int $id
 * @property string $name
 * @property bool $status
 * @property AvailabilityHours $avaibilityHours
 */
class PaymentMethod extends P24ResponseObject
{
    /**
     * @var string[]
     */
    protected $keys = [
        'id',
        'name',
        'status',
        'avaibilityHours',
    ];

    /**
     * PaymentMethod constructor.
     * @param null $response
     */
    public function __construct($response = null)
    {
        parent::__construct($response);

        if (!empty($this->data['avaibilityHours'])) {
            $this->data['avaibilityHours'] = new AvailabilityHours($this->data['avaibilityHours']);
        }
    }
}
