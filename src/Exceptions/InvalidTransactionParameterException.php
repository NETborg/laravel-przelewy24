<?php

namespace NetborgTeam\P24\Exceptions;

/**
 * Description of InvalidMerchantIdException
 *
 * @author netborg
 */
class InvalidTransactionParameterException extends \Exception
{
    protected $paramName;

    protected $expectedValue;

    protected $receivedValue;

    public function __construct($paramName, $expectedValue, $receivedValue)
    {
        $this->paramName = $paramName;
        $this->expectedValue = $expectedValue;
        $this->receivedValue;

        $message = "Invalid P24Transaction parameter [$paramName]: expected value `$expectedValue`, received `$receivedValue`.";
        parent::__construct($message, 0, null);
    }

    public function getParameterName()
    {
        return $this->paramName;
    }

    public function getExpectedValue()
    {
        return $this->expectedValue;
    }

    public function getReceivedValue()
    {
        return $this->receivedValue;
    }
}
