<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 12:57
 */

namespace NetborgTeam\P24;

/**
 * Class GeneralError
 * @package NetborgTeam\P24
 *
 * @property int $errorCode
 * @property string $errorMessage
 */
class GeneralError extends P24ResponseObject
{
    /**
     * @var array
     */
    protected $keys = [
        'errorCode',
        'errorMessage'
    ];


    /**
     * GeneralError constructor.
     * @param object|null $response
     */
    public function __construct($response = null)
    {
        parent::__construct($response);

        if ($this->errorCode === null) {
            $this->errorCode = 1000;
            $this->errorMessage = "It's strange ... error status not received from Przelewy24 ...";
        }
    }
}
