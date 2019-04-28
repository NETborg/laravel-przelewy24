<?php

namespace NetborgTeam\P24\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use NetborgTeam\P24\P24TransactionConfirmation;

class P24TransactionConfirmationInvalidParameterEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @var P24TransactionConfirmation
     */
    public $transactionConfirmation;

    /**
     * @var string
     */
    public $parameterName;

    /**
     * @var mixed
     */
    public $expectedValue;

    /**
     * @var mixed
     */
    public $receivedValue;

    /**
     * Create a new event instance.
     *
     * @param P24TransactionConfirmation $transactionConfirmation
     * @param string                     $parameterName
     * @param mixed                      $expectedValue
     * @param mixed                      $receivedValue
     */
    public function __construct(
        P24TransactionConfirmation $transactionConfirmation,
        $parameterName,
        $expectedValue,
        $receivedValue
    ) {
        $this->transactionConfirmation = $transactionConfirmation;
        $this->parameterName = $parameterName;
        $this->expectedValue = $expectedValue;
        $this->receivedValue = $receivedValue;
    }
}
