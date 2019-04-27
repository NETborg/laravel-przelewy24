<?php

namespace NetborgTeam\P24\Exceptions;

/**
 * Description of InvalidMerchantIdException
 *
 * @author netborg
 */
class InvalidApiKeyException extends \Exception
{
    public function __construct(
        $message = "Invalid API KEY. Please check if you have not forgotten to provide a valid P24_API_KEY in `.env` file.",
        $code = 0,
        $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
