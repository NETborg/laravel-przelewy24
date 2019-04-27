<?php
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 09.12.17
 * Time: 12:29
 */

namespace NetborgTeam\P24\Services;

use Carbon\Carbon;
use NetborgTeam\P24\ArrayOfRefund;
use NetborgTeam\P24\Exceptions\InvalidCRCException;
use NetborgTeam\P24\Exceptions\InvalidMerchantIdException;
use NetborgTeam\P24\PaymentMethodsResult;
use NetborgTeam\P24\TransactionFullResult;
use NetborgTeam\P24\TransactionRefundResult;
use NetborgTeam\P24\TransactionShortResult;

class P24WebServicesManager
{
    const ENDPOINT_LIVE = "https://secure.przelewy24.pl/external/wsdl/service.php?wsdl"; //"https://secure.przelewy24.pl/external/{merchant_id}.wsdl";
    const ENDPOINT_SANDBOX = "https://sandbox.przelewy24.pl/external/wsdl/service.php?wsdl"; //"https://sandbox.przelewy24.pl/external/{merchant_id}.wsdl";
    const ENDPOINT_LIVE_CARD = "https://secure.przelewy24.pl/external/wsdl/charge_card_service.php?wsdl";
    const ENDPOINT_SANDBOX_CARD = "https://sandbox.przelewy24.pl/external/wsdl/charge_card_service.php?wsdl";


    public static $CARD_METHODS = [
        'GetTransactionReference',
        'ChargeCard',
        'RecurringChargeCard',
        'CheckCard'
    ];


    private $merchantId;
    private $posId;
    private $crc;
    private $apiKey;
    private $soap;



    public function __construct()
    {
        $this->merchantId = config('p24.merchant_id', 0);
        $this->posId = config('p24.pos_id', 0) > 0 ? config('p24.pos_id', 0) : $this->merchantId;
        $this->crc = config('p24.crc', null);
        $this->apiKey = config('p24.api_key', null);

        if ((int) $this->merchantId === 0) {
            throw new InvalidMerchantIdException();
        }
        if (!$this->crc) {
            throw new InvalidCRCException();
        }

        $this->soap = new \SoapClient($this->endpoint());
    }



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

    public function getFunctions()
    {
        return $this->soap->__getFunctions();
    }

    public function getLastRequest()
    {
        return $this->soap->__getLastRequest();
    }

    public function getLastResponse()
    {
        return $this->soap->__getLastResponse();
    }


    /**
     * Tests connection and authentication to Przelewy24 web service.
     * Returns `true` on successful connection and authentication and `false` otherwise.
     *
     * @return bool
     */
    public function testAccess()
    {
        return $this->soap->TestAccess($this->merchantId, $this->apiKey);
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
            $this->soap->TrnBySessionId(
                $this->merchantId,
                $this->apiKey,
                $sessionId
            )
        );
    }

    /**
     * Gets full transaction details from Przelewy24 web service.
     *
     * @param  string                $sessionId
     * @return TransactionFullResult
     */
    public function getTransactionFullBySessionId($sessionId)
    {
        return new TransactionFullResult(
            $this->soap->TrnFullBySessionId(
                $this->merchantId,
                $this->apiKey,
                $sessionId
            )
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
            $this->soap->TrnRefund(
                $this->merchantId,
                $this->apiKey,
                $batch,
                $refundList->toArray()
            )
        );
    }
}
