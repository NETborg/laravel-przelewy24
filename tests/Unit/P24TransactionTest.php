<?php
declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Carbon;
use Faker\Factory;
use Illuminate\Support\Str;
use NetborgTeam\P24\P24Transaction;
use PHPUnit\Framework\TestCase;

class P24TransactionTest extends TestCase
{

    /**
     * @var P24Transaction
     */
    private $transaction;




    /**
     * @var string
     */
    private $id;

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
    private $p24MerchantId;

    /**
     * @var int
     */
    private $p24Amount;

    /**
     * @var string
     */
    private $p24Currency;

    /**
     * @var string
     */
    private $p24Description;

    /**
     * @var string
     */
    private $p24Email;

    /**
     * @var string
     */
    private $p24Client;

    /**
     * @var string
     */
    private $p24Address;

    /**
     * @var string
     */
    private $p24Zip;

    /**
     * @var string
     */
    private $p24City;

    /**
     * @var string
     */
    private $p24Country;

    /**
     * @var string
     */
    private $p24Phone;

    /**
     * @var string
     */
    private $p24Language;

    /**
     * @var int
     */
    private $p24Method;

    /**
     * @var string
     */
    private $p24UrlReturn;

    /**
     * @var string
     */
    private $p24UrlStatus;

    /**
     * @var int
     */
    private $p24TimeLimit;

    /**
     * @var int
     */
    private $p24WaitForResult;

    /**
     * @var int
     */
    private $p24Channel;

    /**
     * @var int
     */
    private $p24Shipping;

    /**
     * @var string
     */
    private $p24TransferLabel;

    /**
     * @var string
     */
    private $p24Sign;

    /**
     * @var string
     */
    private $p24Encoding;

    /**
     * @var int
     */
    private $p24OrderId;

    /**
     * @var string
     */
    private $p24Statement;

    /**
     * @var string
     */
    private $token;

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
        /**
         * @var Factory
         */
        $faker = Factory::create('pl_PL');

        $this->id = P24Transaction::generateUid();
        $this->p24SessionId = Str::random(100);
        $this->p24MerchantId = 5023456;
        $this->p24PosId = 5023456;
        $this->p24Amount = 299;
        $this->p24Currency = "PLN";
        $this->p24Description = $faker->text;
        $this->p24Email = $faker->email;
        $this->p24Client = $faker->name;
        $this->p24Address = $faker->address;
        $this->p24Zip = $faker->postcode;
        $this->p24City = $faker->city;
        $this->p24Country = $faker->country;
        $this->p24Phone = $faker->phoneNumber;
        $this->p24Language = 'pl';
        $this->p24Method = 2;
        $this->p24UrlReturn = "https://www.mydomain.com/return";
        $this->p24UrlStatus = "https://www.mydomain.com/status";
        $this->p24TimeLimit = 60;
        $this->p24WaitForResult = 0;
        $this->p24Channel = 3;
        $this->p24Shipping = 0;
        $this->p24TransferLabel = "Tytuł przelewu";
        $this->p24Sign = "md5_signature";
        $this->p24Encoding = "UTF-8";
        $this->p24OrderId = 1;
        $this->p24Statement = "Statement";
        $this->token = "[token]";
        $this->createdAt = Carbon::now();
        $this->updatedAt = Carbon::now();



        $this->transaction = new P24Transaction([
            'id' => $this->id,
            'p24_session_id' => $this->p24SessionId,
            'p24_merchant_id' => $this->p24MerchantId,
            'p24_pos_id' => $this->p24PosId,
            'p24_amount' => $this->p24Amount,
            'p24_currency' => $this->p24Currency,
            'p24_description' => $this->p24Description,
            'p24_email' => $this->p24Email,
            'p24_client' => $this->p24Client,
            'p24_address' => $this->p24Address,
            'p24_zip' => $this->p24Zip,
            'p24_city' => $this->p24City,
            'p24_country' => $this->p24Country,
            'p24_phone' => $this->p24Phone,
            'p24_language' => $this->p24Language,
            'p24_method' => $this->p24Method,
            'p24_url_return' => $this->p24UrlReturn,
            'p24_url_status' => $this->p24UrlStatus,
            'p24_time_limit' => $this->p24TimeLimit,
            'p24_wait_for_result' => $this->p24WaitForResult,
            'p24_channel' => $this->p24Channel,
            'p24_shipping' => $this->p24Shipping,
            'p24_transfer_label' => $this->p24TransferLabel,
            'p24_sign' => $this->p24Sign,
            'p24_encoding' => $this->p24Encoding,
            'p24_order_id' => $this->p24OrderId,
            'p24_statement' => $this->p24Statement,
            'token' => $this->token,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
         ]);
    }


    public function testGetters()
    {
        $this->assertSame($this->id, $this->transaction->id);
        $this->assertSame($this->p24SessionId, $this->transaction->p24_session_id);
        $this->assertSame($this->p24MerchantId, $this->transaction->p24_merchant_id);
        $this->assertSame($this->p24PosId, $this->transaction->p24_pos_id);
        $this->assertSame($this->p24Amount, $this->transaction->p24_amount);
        $this->assertSame($this->p24Currency, $this->transaction->p24_currency);
        $this->assertSame($this->p24Description, $this->transaction->p24_description);
        $this->assertSame($this->p24Email, $this->transaction->p24_email);
        $this->assertSame($this->p24Client, $this->transaction->p24_client);
        $this->assertSame($this->p24Address, $this->transaction->p24_address);
        $this->assertSame($this->p24Zip, $this->transaction->p24_zip);
        $this->assertSame($this->p24City, $this->transaction->p24_city);
        $this->assertSame($this->p24Country, $this->transaction->p24_country);
        $this->assertSame($this->p24Phone, $this->transaction->p24_phone);
        $this->assertSame($this->p24Language, $this->transaction->p24_language);
        $this->assertSame($this->p24Method, $this->transaction->p24_method);
        $this->assertSame($this->p24UrlReturn, $this->transaction->p24_url_return);
        $this->assertSame($this->p24UrlStatus, $this->transaction->p24_url_status);
        $this->assertSame($this->p24TimeLimit, $this->transaction->p24_time_limit);
        $this->assertSame($this->p24WaitForResult, $this->transaction->p24_wait_for_result);
        $this->assertSame($this->p24Channel, $this->transaction->p24_channel);
        $this->assertSame($this->p24Shipping, $this->transaction->p24_shipping);
        $this->assertSame($this->p24TransferLabel, $this->transaction->p24_transfer_label);
        $this->assertSame($this->p24Sign, $this->transaction->p24_sign);
        $this->assertSame($this->p24Encoding, $this->transaction->p24_encoding);
        $this->assertSame($this->p24OrderId, $this->transaction->p24_order_id);
        $this->assertSame($this->p24Statement, $this->transaction->p24_statement);
        $this->assertObjectNotHasAttribute('token', $this->transaction);
        $this->assertSame(null, $this->transaction->created_at);
        $this->assertSame(null, $this->transaction->updated_at);

        $this->transaction->token = $this->token;
        $this->assertSame($this->token, $this->transaction->token);
    }


    public function testGenerateUid()
    {
        $uid1 = P24Transaction::generateUid();
        $uid2 = P24Transaction::generateUid();

        $this->assertNotSame($uid1, $uid2);
        $this->assertRegExp('/^[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}$/', $uid1);
        $this->assertRegExp('/^[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}$/', $uid2);
    }

    public function testGetSignablePayload()
    {
        $payload = $this->transaction->getSignablePayload();

        $this->assertIsArray($payload);
        $this->assertSame([
            $this->p24SessionId,
            $this->p24MerchantId,
            $this->p24Amount,
            $this->p24Currency
        ], $payload);
    }

}
