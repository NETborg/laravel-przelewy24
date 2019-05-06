<?php
declare(strict_types=1);

namespace NetborgTeam\P24\Exceptions;

/**
 * Description of InvalidMerchantIdException
 *
 * @author netborg
 */
class InvalidSignatureException extends \Exception
{
    /**
     * InvalidSignatureException constructor.
     * @param string $expectedSign
     * @param string $receivedSign
     */
    public function __construct(string $expectedSign, string $receivedSign)
    {
        $message = "Transaction signature and confirmation/verification signature doesn't match! Expected sign [$expectedSign] but received [$receivedSign].";
        parent::__construct($message, 0, null);
    }
}
