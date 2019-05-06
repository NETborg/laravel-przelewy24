<?php
declare(strict_types=1);

namespace Tests\Unit;


use NetborgTeam\P24\SingleRefund;
use PHPUnit\Framework\TestCase;

class SingleRefundTest extends TestCase
{

    const ORDER_ID = 1;
    const SESSION_ID = "1234567890";
    const AMOUNT = 199;
    const STATUS = 2;
    const ERROR = null;

    private $refund;


    protected function setUp(): void
    {
        $this->refund = new SingleRefund([
            'orderId' => self::ORDER_ID,
            'sessionId' => self::SESSION_ID,
            'amount' => self::AMOUNT,
            'status' => self::STATUS,
            'error' => self::ERROR,
        ]);
    }

    public function testGetters()
    {
        $this->assertSame(self::ORDER_ID, $this->refund->orderId);
        $this->assertSame(self::SESSION_ID, $this->refund->sessionId);
        $this->assertSame(self::AMOUNT, $this->refund->amount);
        $this->assertSame(self::STATUS, $this->refund->status);
        $this->assertSame(self::ERROR, $this->refund->error);
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

        $refund->orderId = self::ORDER_ID;
        $refund->sessionId = self::SESSION_ID;
        $refund->amount = self::AMOUNT;
        $refund->status = self::STATUS;
        $refund->error = self::ERROR;


        $this->assertSame(self::ORDER_ID, $refund->orderId);
        $this->assertSame(self::SESSION_ID, $refund->sessionId);
        $this->assertSame(self::AMOUNT, $refund->amount);
        $this->assertSame(self::STATUS, $refund->status);
        $this->assertSame(self::ERROR, $refund->error);
    }

    public function testGetResponse()
    {
        $this->assertSame([
            'orderId' => self::ORDER_ID,
            'sessionId' => self::SESSION_ID,
            'amount' => self::AMOUNT,
            'status' => self::STATUS,
            'error' => self::ERROR,
        ], $this->refund->getResponse(), "Method getResponse() doesn't return right value.");
    }

    public function testToArray()
    {
        $this->assertIsArray($this->refund->toArray());

        $this->assertSame([
            'orderId' => self::ORDER_ID,
            'sessionId' => self::SESSION_ID,
            'amount' => self::AMOUNT,
            'status' => self::STATUS,
        ], $this->refund->toArray());
    }

    public function testToJson()
    {
        $this->assertJson($this->refund->toJson());

        $this->assertJsonStringEqualsJsonString(json_encode([
            'orderId' => self::ORDER_ID,
            'sessionId' => self::SESSION_ID,
            'amount' => self::AMOUNT,
            'status' => self::STATUS,
        ]), $this->refund->toJson());

        $this->assertJsonStringEqualsJsonString(json_encode([
            'orderId' => self::ORDER_ID,
            'sessionId' => self::SESSION_ID,
            'amount' => self::AMOUNT,
            'status' => self::STATUS,
        ], JSON_PRETTY_PRINT), $this->refund->toJson(JSON_PRETTY_PRINT));
    }

}
