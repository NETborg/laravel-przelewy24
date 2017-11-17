<?php

namespace NetborgTeam\P24\Exceptions;

/**
 * Description of InvalidMerchantIdException
 *
 * @author netborg
 */
class InvalidSenderException extends \Exception {
    
    public function __construct($ip) {
        $message = "Sender's IP [$ip] is not in the valid Przelewy24 server IPs scope.";
        parent::__construct($message, 0, null);
    }
    
}
