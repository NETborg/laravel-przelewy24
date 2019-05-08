<?php
declare(strict_types=1);

namespace Tests\Unit;


use NetborgTeam\P24\PaymentMethodsResult;
use PHPUnit\Framework\TestCase;

class PaymentMethodResultTest extends TestCase
{

    /**
     * @var PaymentMethodsResult
     */
    private $paymentMethodsResult;

    /**
     * @var \stdClass
     */
    private $response;



    protected function setUp(): void
    {
        $this->response = json_decode(file_get_contents(__DIR__ . '/../p24_response_payment_methods.json'));

        $this->paymentMethodsResult = new PaymentMethodsResult($this->response);
    }


    public function testResult()
    {
        $result = $this->paymentMethodsResult->result();

        $this->assertIsArray($result);
        $this->assertCount(count($this->response->result), $result);
    }
}
