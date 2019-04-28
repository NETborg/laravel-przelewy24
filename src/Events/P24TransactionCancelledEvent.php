<?php

namespace NetborgTeam\P24\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use NetborgTeam\P24\P24Transaction;

class P24TransactionCancelledEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @var P24Transaction
     */
    public $transaction;

    /**
     * Create a new event instance.
     *
     * @param P24Transaction $transaction
     */
    public function __construct(P24Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
