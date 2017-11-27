<?php

namespace NetborgTeam\P24\Exceptions;

/**
 * Description of InvalidMerchantIdException
 *
 * @author netborg
 */
class InvalidTransactionParameterException extends \Exception {
    
    public function __construct($paramName, $expectedValue, $receivedValue) {
        $message = "Invalid P24Transaction parameter [$paramName]: expected value `$expectedValue`, received `$receivedValue`.";
        parent::__construct($message, 0, null);
    }
}
