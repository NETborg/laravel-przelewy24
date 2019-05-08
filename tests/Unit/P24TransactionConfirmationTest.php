<?php
declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use NetborgTeam\P24\P24Transaction;
use NetborgTeam\P24\P24TransactionConfirmation;
use PHPUnit\Framework\TestCase;

class P24TransactionConfirmationTest extends TestCase
{

    /**
     * @var P24TransactionConfirmation
     */
    private $confirmation;


    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $p24TransactionId;

    /**
     * @var int
     */
    private $p24MerchantId;

    /**
     * @var int
     */
    private $p24PosId;

    /**
     * @var string
     */
    private $p24SessionId;

    /**
     * @var int
     */
    private $p24OrderId;

    /**
     * @var int
     */
    private $p24Amount;

    /**
     * @var string
     */
    private $p24Currency;

    /**
     * @var int
     */
    private $p24Method;

    /**
     * @var string
     */
    private $p24Statement;

    /**
     * @var string
     */
    private $p24Sign;

    /**
     * @var string
     */
    private $verificationStatus;

    /**
     * @var string|null
     */
    private $verificationSign;

    /**
     * @var Carbon|null
     */
    private $verifiedAt;

    /**
     * @var Carbon
     */
    private $createdAt;

    /**
     * @var Carbon
     */
    private $updatedAt;








    protected function setUp(): void
    {

        $this->id = P24TransactionConfirmation::generateUid();
        $this->p24TransactionId = P24Transaction::generateUid();
        $this->p24SessionId = Str::random(100);
        $this->p24OrderId = 1;
        $this->p24MerchantId = 5023456;
        $this->p24PosId = 5023456;
        $this->p24Amount = 299;
        $this->p24Currency = "PLN";
        $this->p24Method = 2;
        $this->p24Statement = "Statement";
        $this->p24Sign = "signature";
        $this->verificationStatus = P24TransactionConfirmation::STATUS_NEW;
        $this->verificationSign = null;
        $this->verifiedAt = null;
        $this->createdAt = Carbon::now();
        $this->updatedAt = Carbon::now();

        $this->confirmation = new P24TransactionConfirmation([
            'id' => $this->id,
            'p24_merchant_id' => $this->p24MerchantId,
            'p24_pos_id' => $this->p24PosId,
            'p24_transaction_id' => $this->p24TransactionId,
            'p24_session_id' => $this->p24SessionId,
            'p24_order_id' => $this->p24OrderId,
            'p24_amount' => $this->p24Amount,
            'p24_currency' => $this->p24Currency,
            'p24_method' => $this->p24Method,
            'p24_statement' => $this->p24Statement,
            'p24_sign' => $this->p24Sign,
            'verification_status' => $this->verificationStatus,
            'verification_sign' => $this->verificationSign,
            'verified_at' => $this->verifiedAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ]);
    }

    public function testGetters()
    {
        $this->assertSame($this->id, $this->confirmation->id);
        $this->assertSame($this->p24TransactionId, $this->confirmation->p24_transaction_id);
        $this->assertSame($this->p24SessionId, $this->confirmation->p24_session_id);
        $this->assertSame($this->p24MerchantId, $this->confirmation->p24_merchant_id);
        $this->assertSame($this->p24PosId, $this->confirmation->p24_pos_id);
        $this->assertSame($this->p24Amount, $this->confirmation->p24_amount);
        $this->assertSame($this->p24Currency, $this->confirmation->p24_currency);
        $this->assertSame($this->p24Method, $this->confirmation->p24_method);
        $this->assertSame($this->p24Sign, $this->confirmation->p24_sign);
        $this->assertSame($this->p24OrderId, $this->confirmation->p24_order_id);
        $this->assertSame($this->p24Statement, $this->confirmation->p24_statement);
        $this->assertObjectNotHasAttribute('verification_status', $this->confirmation);
        $this->assertObjectNotHasAttribute('verification_sign', $this->confirmation);
        $this->assertObjectNotHasAttribute('verified_at', $this->confirmation);
        $this->assertSame(null, $this->confirmation->created_at);
        $this->assertSame(null, $this->confirmation->updated_at);
    }

    public function testGenerateUid()
    {
        $uid1 = P24TransactionConfirmation::generateUid();
        $uid2 = P24TransactionConfirmation::generateUid();

        $this->assertNotSame($uid1, $uid2);
        $this->assertRegExp('/^[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}$/', $uid1);
        $this->assertRegExp('/^[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}$/', $uid2);
    }

    public function testGetSignablePayload()
    {
        $payload = $this->confirmation->getSignablePayload();

        $this->assertIsArray($payload);
        $this->assertSame([
            $this->p24SessionId,
            $this->p24OrderId,
            $this->p24Amount,
            $this->p24Currency,
        ], $payload);
    }

}
