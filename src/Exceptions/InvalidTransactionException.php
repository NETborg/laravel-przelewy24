<?php

namespace NetborgTeam\P24\Exceptions;

/**
 * Description of InvalidMerchantIdException
 *
 * @author netborg
 */
class InvalidTransactionException extends \Exception
{
    public function __construct()
    {
        $message = "Invalid P24Transaction.";
        parent::__construct($message, 0, null);
    }
}
