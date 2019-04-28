<?php

namespace NetborgTeam\P24\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use NetborgTeam\P24\P24TransactionConfirmation;

class P24TransactionConfirmationConnectionErrorEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @var P24TransactionConfirmation
     */
    public $transactionConfirmation;

    /**
     * @var int
     */
    public $errorCode;

    /**
     * @var string
     */
    public $errorMessage;


    /**
     * Create a new event instance.
     *
     * @param P24TransactionConfirmation $transactionConfirmation
     * @param int                        $errorCode
     * @param string                     $errorMessage
     */
    public function __construct(
        P24TransactionConfirmation $transactionConfirmation,
        $errorCode,
        $errorMessage
    ) {
        $this->transactionConfirmation = $transactionConfirmation;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }
}
