<?php

namespace NetborgTeam\P24\Exceptions;

/**
 * Description of InvalidMerchantIdException
 *
 * @author netborg
 */
class InvalidSignatureException extends \Exception
{
    public function __construct($expectedSign, $receivedSign)
    {
        $message = "Transaction signature and confirmation/verification signature doesn't match! Expected sign [$expectedSign] but received [$receivedSign].";
        parent::__construct($message, 0, null);
    }
}
