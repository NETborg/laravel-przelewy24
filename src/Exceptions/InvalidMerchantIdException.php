<?php

namespace NetborgTeam\P24\Exceptions;

/**
 * Description of InvalidMerchantIdException
 *
 * @author netborg
 */
class InvalidMerchantIdException extends \Exception
{
    public function __construct(
        $message = "Invalid MERCHANT ID. Please check if you have not forgotten to provide a valid P24_MERCHANT_ID and/or P24_POS_ID in `.env` file.",
        $code = 0,
        $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
