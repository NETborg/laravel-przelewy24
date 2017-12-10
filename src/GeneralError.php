<?php
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 12:57
 */

namespace NetborgTeam\P24;


class GeneralError extends P24ResponseObject
{

    protected $keys = [
        'errorCode',
        'errorMessage'
    ];


    public function __construct($response = null)
    {
        parent::__construct($response);

        if ($this->errorCode === null) {
            $this->errorCode = 1000;
            $this->errorMessage = "It's strange ... error status not received from Przelewy24 ...";
        }
    }

}