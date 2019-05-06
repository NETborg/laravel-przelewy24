<?php
namespace Tests\Unit;

use NetborgTeam\P24\ArrayOfRefund;
use NetborgTeam\P24\SingleRefund;
use PHPUnit\Framework\TestCase;

class ArrayOfRefundTest extends TestCase
{
    /**
     * @var SingleRefund[]
     */
    private $refunds;

    /**
     * @var SingleRefund[]
     */
    private $refundsCheckArray;

    protected function setUp(): void
    {
        $this->refunds[] = new SingleRefund([
            'orderId' => 1,
            'sessionId' => "123asw",
            'amount' => 199,
            'status' => 1,
            'error' => null,
        ]);

        $this->refunds[] = new SingleRefund([
            'orderId' => 2,
            'sessionId' => "456",
            'amount' => 300,
            'status' => 2,
            'error' => "error",
        ]);

        $this->refunds[] = new SingleRefund([
            'orderId' => 3,
            'sessionId' => "789sdf",
            'amount' => 50,
            'status' => 3,
            'error' => "error_message",
        ]);



        $this->refundsCheckArray[] = [
            'orderId' => 1,
            'sessionId' => "123asw",
            'amount' => 199,
            'status' => 1,
        ];

        $this->refundsCheckArray[] = [
            'orderId' => 2,
            'sessionId' => "456",
            'amount' => 300,
            'status' => 2,
            'error' => "error",
        ];

        $this->refundsCheckArray[] = [
            'orderId' => 3,
            'sessionId' => "789sdf",
            'amount' => 50,
            'status' => 3,
            'error' => "error_message",
        ];
    }

    public function testAddAndToArray()
    {
        $arrayOfRefund = new ArrayOfRefund();
        $arrayOfRefund
            ->add($this->refunds[0])
            ->add($this->refunds[1])
            ->add($this->refunds[2])
            ->add($this->refunds[0])
            ->add($this->refunds[1])
            ->add($this->refunds[2])
        ;

        $this->assertCount(3, $arrayOfRefund->toArray());
        $this->assertSame($this->refundsCheckArray, $arrayOfRefund->toArray());
    }

    public function testHas()
    {
        $arrayOfRefund = new ArrayOfRefund();
        $arrayOfRefund
            ->add($this->refunds[0])
            ->add($this->refunds[1])
            ->add($this->refunds[2])
        ;

        $this->assertSame(true, $arrayOfRefund->has($this->refunds[2]));
        $this->assertSame(true, $arrayOfRefund->has("123asw"));

        $this->assertSame(false, $arrayOfRefund->has(new SingleRefund([
            'orderId' => 1,
            'sessionId' => "321",
            'amount' => 199,
            'status' => 1,
            'error' => null,
        ])));
        $this->assertSame(false, $arrayOfRefund->has("321"));
    }

    public function testGet()
    {
        $arrayOfRefund = new ArrayOfRefund();
        $arrayOfRefund
            ->add($this->refunds[0])
            ->add($this->refunds[1])
            ->add($this->refunds[2])
        ;

        $this->assertSame($this->refunds[1], $arrayOfRefund->get(1));
        $this->assertSame($this->refunds[0], $arrayOfRefund->get("123asw"));

        $this->assertSame(null, $arrayOfRefund->get(4));
        $this->assertSame(null, $arrayOfRefund->get("123aswa"));
    }

    public function testRemove()
    {
        $arrayOfRefund = new ArrayOfRefund();
        $arrayOfRefund
            ->add($this->refunds[0])
            ->add($this->refunds[1])
            ->add($this->refunds[2])
        ;

        $this->assertSame($this->refunds[1], $arrayOfRefund->get(1));
        $this->assertSame($this->refunds[0], $arrayOfRefund->get("123asw"));

        $arrayOfRefund->remove($this->refunds[1]);
        $arrayOfRefund->remove("123asw");

        $this->assertSame(null, $arrayOfRefund->get(1));
        $this->assertSame(null, $arrayOfRefund->get("123asw"));

        $this->assertCount(1, $arrayOfRefund->toArray());
        $this->assertSame($this->refunds[2], $arrayOfRefund->get("789sdf"));
        $this->assertSame($this->refunds[2], $arrayOfRefund->get(2));

    }

    public function testAddByKeys()
    {
        $arrayOfRefund = new ArrayOfRefund();
        $arrayOfRefund
            ->addByKeys("123aaa", 1, 500)
            ->addByKeys("321qwe", 2, 122)
            ->addByKeys("432sdf", 3, 999)
            ->addByKeys("123aaa", 1, 500)
            ->addByKeys("321qwe", 2, 122)
            ->addByKeys("432sdf", 3, 999)
        ;

        $this->assertCount(3, $arrayOfRefund->toArray());
        $this->assertSame(true, $arrayOfRefund->has("123aaa"));
        $this->assertSame(true, $arrayOfRefund->has("321qwe"));
        $this->assertSame(true, $arrayOfRefund->has("432sdf"));
    }
}
