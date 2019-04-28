<?php

namespace NetborgTeam\P24\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use NetborgTeam\P24\P24TransactionConfirmation;

class P24TransactionConfirmationInvalidSignatureEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @var P24TransactionConfirmation
     */
    public $transactionConfirmation;

    /**
     * Create a new event instance.
     *
     * @param P24TransactionConfirmation $transactionConfirmation
     */
    public function __construct(P24TransactionConfirmation $transactionConfirmation)
    {
        $this->transactionConfirmation = $transactionConfirmation;
    }
}
