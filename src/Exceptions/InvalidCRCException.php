<?php

namespace NetborgTeam\P24\Exceptions;

/**
 * Description of InvalidMerchantIdException
 *
 * @author netborg
 */
class InvalidCRCException extends \Exception {
    
    public function __construct(
            $message = "Invalid CRC. Please check if you have not forgotten to provide a valid P24_CRC in `.env` file.", 
            $code = 0, 
            $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
    
}
