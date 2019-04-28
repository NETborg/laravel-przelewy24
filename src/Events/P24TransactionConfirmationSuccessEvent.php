<?php

namespace NetborgTeam\P24\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use NetborgTeam\P24\P24Transaction;
use NetborgTeam\P24\P24TransactionConfirmation;

class P24TransactionConfirmationSuccessEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @var P24Transaction
     */
    public $transaction;

    /**
     * @var P24TransactionConfirmation
     */
    public $transactionConfirmation;

    /**
     * Create a new event instance.
     *
     * @param P24Transaction             $transaction
     * @param P24TransactionConfirmation $transactionConfirmation
     */
    public function __construct(P24Transaction $transaction, P24TransactionConfirmation $transactionConfirmation)
    {
        $this->transaction = $transaction;
        $this->transactionConfirmation = $transactionConfirmation;
    }
}
