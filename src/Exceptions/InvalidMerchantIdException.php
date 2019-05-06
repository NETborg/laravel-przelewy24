<?php
declare(strict_types=1);

namespace NetborgTeam\P24\Exceptions;

/**
 * Description of InvalidMerchantIdException
 *
 * @author netborg
 */
class InvalidMerchantIdException extends \Exception
{
    /**
     * InvalidMerchantIdException constructor.
     * @param string          $message
     * @param int             $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = "Invalid MERCHANT ID. Please check if you have not forgotten to provide a valid P24_MERCHANT_ID and/or P24_POS_ID in `.env` file.",
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
