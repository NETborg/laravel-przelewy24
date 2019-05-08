<?php
declare(strict_types=1);

namespace Tests\Unit;


use NetborgTeam\P24\SingleRefund;
use PHPUnit\Framework\TestCase;

class SingleRefundTest extends TestCase
{

    /**
     * @var SingleRefund
     */
    private $refund;

    /**
     * @var \stdClass
     */
    private $response;




    /**
     * @var int
     */
    private $orderId;

    /**
     * @var string
     */
    private $sessionId;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var int
     */
    private $status;

    /**
     * @var string|null
     */
    private $error;




    protected function setUp(): void
    {
        $this->orderId = 1;
        $this->sessionId = "1234567890";
        $this->amount = 199;
        $this->status = 2;
        $this->error = null;

        $this->response = new \stdClass();
        $this->response->orderId = $this->orderId;
        $this->response->sessionId = $this->sessionId;
        $this->response->amount = $this->amount;
        $this->response->status = $this->status;
        $this->response->error = $this->error;

        $this->refund = new SingleRefund($this->response);
    }

    public function testGetters()
    {
        $this->assertSame($this->orderId, $this->refund->orderId);
        $this->assertSame($this->sessionId, $this->refund->sessionId);
        $this->assertSame($this->amount, $this->refund->amount);
        $this->assertSame($this->status, $this->refund->status);
        $this->assertSame($this->error, $this->refund->error);
    }

    public function testSetters()
    {
        $refund = new SingleRefund([
            'orderId' => 5,
            'sessionId' => "0987654321",
            'amount' => 411,
            'status' => 1,
            'error' => "error_message",
        ]);

        $refund->orderId = $this->orderId;
        $refund->sessionId = $this->sessionId;
        $refund->amount = $this->amount;
        $refund->status = $this->status;
        $refund->error = $this->error;


        $this->assertSame($this->orderId, $refund->orderId);
        $this->assertSame($this->sessionId, $refund->sessionId);
        $this->assertSame($this->amount, $refund->amount);
        $this->assertSame($this->status, $refund->status);
        $this->assertSame($this->error, $refund->error);
    }

    public function testGetResponse()
    {
        $this->assertSame(
            $this->response,
            $this->refund->getResponse(),
            "Method getResponse() doesn't return right value."
        );
    }

    public function testToArray()
    {
        $this->assertIsArray($this->refund->toArray());

        $this->assertSame([
            'orderId' => $this->orderId,
            'sessionId' => $this->sessionId,
            'amount' => $this->amount,
            'status' => $this->status,
        ], $this->refund->toArray());
    }

    public function testToJson()
    {
        $this->assertJson($this->refund->toJson());

        $this->assertJsonStringEqualsJsonString(json_encode([
            'orderId' => $this->orderId,
            'sessionId' => $this->sessionId,
            'amount' => $this->amount,
            'status' => $this->status,
        ]), $this->refund->toJson());

        $this->assertJsonStringEqualsJsonString(json_encode([
            'orderId' => $this->orderId,
            'sessionId' => $this->sessionId,
            'amount' => $this->amount,
            'status' => $this->status,
        ], JSON_PRETTY_PRINT), $this->refund->toJson(JSON_PRETTY_PRINT));
    }

}
