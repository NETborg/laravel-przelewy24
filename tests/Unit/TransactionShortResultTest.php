<?php
declare(strict_types=1);

namespace Tests\Unit;


use NetborgTeam\P24\TransactionRefundResult;
use NetborgTeam\P24\TransactionShortResult;
use PHPUnit\Framework\TestCase;

class TransactionShortResultTest extends TestCase
{
    /**
     * @var TransactionRefundResult
     */
    private $transactionShortResult;

    /**
     * @var \stdClass
     */
    private $response;




    protected function setUp(): void
    {
        $this->response = json_decode(file_get_contents(__DIR__ . '/../p24_response_get_transaction_by_session_id.json'));

        $this->transactionShortResult = new TransactionShortResult($this->response);
    }


    public function testResult()
    {
        $result = $this->transactionShortResult->result();

        $this->assertIsObject($result);
        $this->assertSame(300093526, $result->orderId);
    }

}
