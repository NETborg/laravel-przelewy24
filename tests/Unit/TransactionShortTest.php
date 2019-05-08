<?php
declare(strict_types=1);

namespace Tests\Unit;


use Illuminate\Support\Carbon;
use Faker\Factory;
use NetborgTeam\P24\TransactionShort;
use PHPUnit\Framework\TestCase;

class TransactionShortTest extends TestCase
{

    /**
     * @var TransactionShort
     */
    private $transaction;

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
    private $status;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string|Carbon
     */
    private $date;

    /**
     * @var string|Carbon
     */
    private $dateOfTransaction;

    /**
     * @var string
     */
    private $clientEmail;

    /**
     * @var string
     */
    private $accountMD5;

    /**
     * @var int
     */
    private $paymentMethod;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $clientAddress;

    /**
     * @var string
     */
    private $clientCity;

    /**
     * @var string
     */
    private $clientName;

    /**
     * @var string
     */
    private $clientPostcode;

    /**
     * @var int
     */
    private $batchId;

    /**
     * @var int
     */
    private $fee;

    /**
     * @var string
     */
    private $statement;




    protected function setUp(): void
    {
        $faker = Factory::create('pl_PL');

        $this->orderId = 1;
        $this->sessionId = "1234567890awsertyu";
        $this->status = 2;
        $this->amount = 321;
        $this->currency = "PLN";
        $this->date = "201905081056";
        $this->dateOfTransaction = "201905081109";
        $this->clientEmail = $faker->email;
        $this->accountMD5 = md5($name = $faker->name);
        $this->paymentMethod = 23;
        $this->description = $faker->text;
        $this->clientAddress = $faker->address;
        $this->clientCity = $faker->city;
        $this->clientName = $name;
        $this->clientPostcode = $faker->postcode;
        $this->batchId = 6;
        $this->fee = 29;
        $this->statement = "Tytuł przelewu.";

        $this->response = new \stdClass();
        $this->response->orderId = $this->orderId;
        $this->response->sessionId = $this->sessionId;
        $this->response->status = $this->status;
        $this->response->amount = $this->amount;
        $this->response->currency = $this->currency;
        $this->response->date = $this->date;
        $this->response->dateOfTransaction = $this->dateOfTransaction;
        $this->response->clientEmail = $this->clientEmail;
        $this->response->accountMD5 = $this->accountMD5;
        $this->response->paymentMethod = $this->paymentMethod;
        $this->response->description = $this->description;
        $this->response->clientAddress = $this->clientAddress;
        $this->response->clientCity = $this->clientCity;
        $this->response->clientName = $this->clientName;
        $this->response->clientPostcode = $this->clientPostcode;
        $this->response->batchId = $this->batchId;
        $this->response->fee = $this->fee;
        $this->response->statement = $this->statement;

        $this->transaction = new TransactionShort($this->response);
    }

    public function testGetters()
    {
        $this->assertSame($this->orderId, $this->transaction->orderId);
        $this->assertSame($this->sessionId, $this->transaction->sessionId);
        $this->assertSame($this->status, $this->transaction->status);
        $this->assertSame($this->amount, $this->transaction->amount);
        $this->assertSame($this->currency, $this->transaction->currency);
        $this->assertInstanceOf(Carbon::class, $this->transaction->date);
        $this->assertSame($this->date, $this->transaction->date->format('YmdHi'));
        $this->assertInstanceOf(Carbon::class, $this->transaction->dateOfTransaction);
        $this->assertSame($this->dateOfTransaction, $this->transaction->dateOfTransaction->format('YmdHi'));
        $this->assertSame($this->clientEmail, $this->transaction->clientEmail);
        $this->assertSame($this->accountMD5, $this->transaction->accountMD5);
        $this->assertSame($this->paymentMethod, $this->transaction->paymentMethod);
        $this->assertSame($this->description, $this->transaction->description);
        $this->assertSame($this->clientAddress, $this->transaction->clientAddress);
        $this->assertSame($this->clientCity, $this->transaction->clientCity);
        $this->assertSame($this->clientName, $this->transaction->clientName);
        $this->assertSame($this->clientPostcode, $this->transaction->clientPostcode);
        $this->assertSame($this->batchId, $this->transaction->batchId);
        $this->assertSame($this->fee, $this->transaction->fee);
        $this->assertSame($this->statement, $this->transaction->statement);
    }

    public function testSetters()
    {
        $transaction = new TransactionShort([
            'orderId' => 13,
            'sessionId' => "0987654321lkjhgf",
            'status' => 1,
            'amount' => 666,
            'currency' => "EUR",
            'date' => "201801010000",
            'dateOfTransaction' => "201801012359",
            'clientEmail' => "noemail@example.com",
            'accountMD5' => md5($name = "Nieznany Klient"),
            'paymentMethod' => 13,
            'description' => "Jakiś fakowy opis.",
            'clientAddress' => "ul. Czekoladowa nr. zlizany",
            'clientCity' => "Wyimaginowane Miasto",
            'clientName' => $name,
            'clientPostcode' => "99-999",
            'batchId' => 16,
            'fee' => 66,
            'statement' => "Fakowy tytuł przelewu.",
        ]);

        $transaction->orderId = $this->orderId;
        $transaction->sessionId = $this->sessionId;
        $transaction->status = $this->status;
        $transaction->amount = $this->amount;
        $transaction->currency = $this->currency;
        $transaction->date = $this->date;
        $transaction->dateOfTransaction = $this->dateOfTransaction;
        $transaction->clientEmail = $this->clientEmail;
        $transaction->accountMD5 = $this->accountMD5;
        $transaction->paymentMethod = $this->paymentMethod;
        $transaction->description = $this->description;
        $transaction->clientAddress = $this->clientAddress;
        $transaction->clientCity = $this->clientCity;
        $transaction->clientName = $this->clientName;
        $transaction->clientPostcode = $this->clientPostcode;
        $transaction->batchId = $this->batchId;
        $transaction->fee = $this->fee;
        $transaction->statement = $this->statement;


        $this->assertSame($this->orderId, $this->transaction->orderId);
        $this->assertSame($this->sessionId, $this->transaction->sessionId);
        $this->assertSame($this->status, $this->transaction->status);
        $this->assertSame($this->amount, $this->transaction->amount);
        $this->assertSame($this->currency, $this->transaction->currency);
        $this->assertInstanceOf(Carbon::class, $this->transaction->date);
        $this->assertSame($this->date, $this->transaction->date->format('YmdHi'));
        $this->assertInstanceOf(Carbon::class, $this->transaction->dateOfTransaction);
        $this->assertSame($this->dateOfTransaction, $this->transaction->dateOfTransaction->format('YmdHi'));
        $this->assertSame($this->clientEmail, $this->transaction->clientEmail);
        $this->assertSame($this->accountMD5, $this->transaction->accountMD5);
        $this->assertSame($this->paymentMethod, $this->transaction->paymentMethod);
        $this->assertSame($this->description, $this->transaction->description);
        $this->assertSame($this->clientAddress, $this->transaction->clientAddress);
        $this->assertSame($this->clientCity, $this->transaction->clientCity);
        $this->assertSame($this->clientName, $this->transaction->clientName);
        $this->assertSame($this->clientPostcode, $this->transaction->clientPostcode);
        $this->assertSame($this->batchId, $this->transaction->batchId);
        $this->assertSame($this->fee, $this->transaction->fee);
        $this->assertSame($this->statement, $this->transaction->statement);
    }

    public function testGetResponse()
    {
        $this->assertSame(
            $this->response,
            $this->transaction->getResponse(),
            "Method getResponse() doesn't return right value."
        );
    }

    public function testToArray()
    {
        $this->assertIsArray($this->transaction->toArray());

        $this->assertSame([
            'orderId' => $this->orderId,
            'sessionId' => $this->sessionId,
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'date' => $this->date,
            'dateOfTransaction' => $this->dateOfTransaction,
            'clientEmail' => $this->clientEmail,
            'accountMD5' => $this->accountMD5,
            'paymentMethod' => $this->paymentMethod,
            'description' => $this->description,
            'clientAddress' => $this->clientAddress,
            'clientCity' => $this->clientCity,
            'clientName' => $this->clientName,
            'clientPostcode' => $this->clientPostcode,
            'batchId' => $this->batchId,
            'fee' => $this->fee,
            'statement' => $this->statement,
        ], $this->transaction->toArray());
    }

    public function testToJson()
    {
        $expected = [
            'orderId' => $this->orderId,
            'sessionId' => $this->sessionId,
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'date' => $this->date,
            'dateOfTransaction' => $this->dateOfTransaction,
            'clientEmail' => $this->clientEmail,
            'accountMD5' => $this->accountMD5,
            'paymentMethod' => $this->paymentMethod,
            'description' => $this->description,
            'clientAddress' => $this->clientAddress,
            'clientCity' => $this->clientCity,
            'clientName' => $this->clientName,
            'clientPostcode' => $this->clientPostcode,
            'batchId' => $this->batchId,
            'fee' => $this->fee,
            'statement' => $this->statement,
        ];

        $this->assertJson($this->transaction->toJson());
        $this->assertJsonStringEqualsJsonString(json_encode($expected), $this->transaction->toJson());
        $this->assertJsonStringEqualsJsonString(json_encode($expected, JSON_PRETTY_PRINT), $this->transaction->toJson(JSON_PRETTY_PRINT));
    }

}
