<?php

namespace NetborgTeam\P24\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use NetborgTeam\P24\P24TransactionConfirmation;

class P24TransactionConfirmationInvalidSenderEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @var P24TransactionConfirmation
     */
    public $transactionConfirmation;

    /**
     * @var string|null
     */
    public $senderIp;

    /**
     * Create a new event instance.
     *
     * @param P24TransactionConfirmation $transactionConfirmation
     * @param string|null                $senderIp
     */
    public function __construct(P24TransactionConfirmation $transactionConfirmation, $senderIp)
    {
        $this->transactionConfirmation = $transactionConfirmation;
        $this->senderIp = $senderIp;
    }
}
