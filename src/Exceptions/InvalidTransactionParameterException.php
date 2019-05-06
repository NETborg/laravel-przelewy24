<?php
declare(strict_types=1);

namespace NetborgTeam\P24\Exceptions;

/**
 * Description of InvalidMerchantIdException
 *
 * @author netborg
 */
class InvalidTransactionParameterException extends \Exception
{
    /**
     * @var string
     */
    protected $paramName;

    /**
     * @var mixed
     */
    protected $expectedValue;

    /**
     * @var mixed
     */
    protected $receivedValue;


    /**
     * InvalidTransactionParameterException constructor.
     * @param string $paramName
     * @param mixed  $expectedValue
     * @param mixed  $receivedValue
     */
    public function __construct(string $paramName, $expectedValue, $receivedValue)
    {
        $this->paramName = $paramName;
        $this->expectedValue = $expectedValue;
        $this->receivedValue;

        $message = "Invalid P24Transaction parameter [$paramName]: expected value `$expectedValue`, received `$receivedValue`.";
        parent::__construct($message, 0, null);
    }

    /**
     * @return string
     */
    public function getParameterName(): string
    {
        return $this->paramName;
    }

    /**
     * @return mixed
     */
    public function getExpectedValue()
    {
        return $this->expectedValue;
    }

    /**
     * @return mixed
     */
    public function getReceivedValue()
    {
        return $this->receivedValue;
    }
}
