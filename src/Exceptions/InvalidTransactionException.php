<?php
declare(strict_types=1);

namespace NetborgTeam\P24\Exceptions;

/**
 * Description of InvalidMerchantIdException
 *
 * @author netborg
 */
class InvalidTransactionException extends \Exception
{
    /**
     * InvalidTransactionException constructor.
     */
    public function __construct()
    {
        $message = "Invalid P24Transaction.";
        parent::__construct($message, 0, null);
    }
}
