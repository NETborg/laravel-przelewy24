<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 09.12.17
 * Time: 12:29
 */

namespace NetborgTeam\P24\Services;

use NetborgTeam\P24\ArrayOfRefund;
use NetborgTeam\P24\Exceptions\InvalidCRCException;
use NetborgTeam\P24\Exceptions\InvalidMerchantIdException;
use NetborgTeam\P24\Exceptions\P24ConnectionException;
use NetborgTeam\P24\PaymentMethodsResult;
use NetborgTeam\P24\TransactionRefundResult;
use NetborgTeam\P24\TransactionShortResult;

class P24WebServicesManager
{
    const ENDPOINT_LIVE = "https://secure.przelewy24.pl/external/{merchant_id}.wsdl";
    const ENDPOINT_SANDBOX = "https://sandbox.przelewy24.pl/external/{merchant_id}.wsdl";
    const ENDPOINT_LIVE_CARD = "https://secure.przelewy24.pl/external/wsdl/charge_card_service.php?wsdl";
    const ENDPOINT_SANDBOX_CARD = "https://sandbox.przelewy24.pl/external/wsdl/charge_card_service.php?wsdl";

    const STATUS_NO_PAYMENT = "no_payment";
    const STATUS_PREPAID = "prepaid";
    const STATUS_PAID = "paid";
    const STATUS_RETURNED = "returned";

    /**
     * @return String[]
     */
    public static function statuses()
    {
        return [
            self::STATUS_NO_PAYMENT,
            self::STATUS_PREPAID,
            self::STATUS_PAID,
            self::STATUS_RETURNED,
        ];
    }

    /**
     * @param  int         $status
     * @return String|null
     */
    public static function translateStatus($status)
    {
        $statuses = self::statuses();
        if (isset($statuses[(int) $status])) {
            return $statuses[(int) $status];
        }

        return null;
    }


    /**
     * @var String[]
     */
    public static $CARD_METHODS = [
        'GetTransactionReference',
        'ChargeCard',
        'RecurringChargeCard',
        'CheckCard'
    ];


    /**
     * @var int
     */
    private $merchantId;

    /**
     * @var int
     */
    private $posId;

    /**
     * @var string|null
     */
    private $crc;

    /**
     * @var string|null
     */
    private $apiKey;

    /**
     * @var \SoapClient
     */
    private $soap;


    /**
     * P24WebServicesManager constructor.
     * @param array $config
     *
     * @throws InvalidCRCException
     * @throws InvalidMerchantIdException
     * @throws P24ConnectionException
     * @throws \SoapFault
     */
    public function __construct(array $config)
    {
        $this->merchantId = (int) $config['merchant_id'] ?? 0;
        $this->posId = isset($config['pos_id']) ? (int) $config['pos_id'] : $this->merchantId;
        $this->crc = $config['crc'] ?? null;
        $this->apiKey = $config['api_key'] ?? null;

        if (!$this->merchantId) {
            throw new InvalidMerchantIdException();
        }
        if (!$this->crc) {
            throw new InvalidCRCException();
        }

        $this->soap = new \SoapClient($this->endpoint());
        if (!$this->testAccess()) {
            throw new P24ConnectionException("Access denied!", 1);
        }
    }


    /**
     * @param  null         $method
     * @return mixed|string
     */
    protected function endpoint($method=null)
    {
        if ($method && in_array(camel_case($method), static::$CARD_METHODS)) {
            if (config('p24.mode') === 'live') {
                $endpoint = self::ENDPOINT_LIVE_CARD;
            } else {
                $endpoint = self::ENDPOINT_SANDBOX_CARD;
            }
        } else {
            if (config('p24.mode') === 'live') {
                $endpoint = str_replace('{merchant_id}', $this->merchantId, self::ENDPOINT_LIVE);
            } else {
                $endpoint = str_replace('{merchant_id}', $this->merchantId, self::ENDPOINT_SANDBOX);
            }
        }

        return $endpoint;
    }


    /**
     * Tests connection and authentication to Przelewy24 web service.
     * Returns `true` on successful connection and authentication and `false` otherwise.
     *
     * @return bool
     */
    public function testAccess()
    {
        return (bool) $this->soap->TestAccess($this->merchantId, $this->apiKey);
    }

    /**
     * Gets list of payment methods from Przelewy24 available for your account.
     *
     * @param  string               $lang
     * @return PaymentMethodsResult
     */
    public function getPaymentMethods($lang='pl')
    {
        return new PaymentMethodsResult(
            $this->soap->PaymentMethods(
                $this->merchantId,
                $this->apiKey,
                $lang
            )
        );
    }

    /**
     * Gets short transaction details from Przelewy24 web service.
     *
     * @param  string                 $sessionId
     * @return TransactionShortResult
     */
    public function getTransactionBySessionId($sessionId)
    {
        return new TransactionShortResult(
            $this->soap->GetTransactionBySessionId(
                $this->merchantId,
                $this->apiKey,
                $sessionId
            )
        );
    }

    /**
     * @param $orderId
     * @param $sessionId
     * @param $amount
     * @param  string $currency
     * @return mixed
     */
    public function getVerifyTransactionResult($orderId, $sessionId, $amount, $currency='PLN')
    {
        return $this->soap->VerifyTransaction(
            $this->merchantId,
            $this->apiKey,
            $orderId,
            $sessionId,
            $amount,
            $currency
        );
    }

    /**
     * Issues refunds for provided transaction list.
     *
     * @param  int                     $batch
     * @param  ArrayOfRefund           $refundList
     * @return TransactionRefundResult
     */
    public function refund($batch, ArrayOfRefund $refundList)
    {
        return new TransactionRefundResult(
            $this->soap->RefundTransaction(
                $this->merchantId,
                $this->apiKey,
                $batch,
                $refundList->toArray()
            )
        );
    }
}
