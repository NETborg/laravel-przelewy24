<?php
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 09.12.17
 * Time: 12:29
 */

namespace NetborgTeam\P24\Services;


use NetborgTeam\P24\Exceptions\InvalidCRCException;
use NetborgTeam\P24\Exceptions\InvalidMerchantIdException;

class P24WebServicesManager
{

    const ENDPOINT_LIVE = "https://secure.przelewy24.pl/external/wsdl/service.php?wsdl"; //"https://secure.przelewy24.pl/external/{merchant_id}.wsdl";
    const ENDPOINT_SANDBOX = "https://sandbox.przelewy24.pl/external/wsdl/service.php?wsdl"; //"https://sandbox.przelewy24.pl/external/{merchant_id}.wsdl";
    const ENDPOINT_LIVE_CARD = "https://secure.przelewy24.pl/external/wsdl/charge_card_service.php?wsdl";
    const ENDPOINT_SANDBOX_CARD = "https://sandbox.przelewy24.pl/external/wsdl/charge_card_service.php?wsdl";


    static $CARD_METHODS = [
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
        $this->soap = new \SoapClient($this->endpoint('GetFunctions'));
        return $this->soap->__getFunctions();
    }

    public function testAccess()
    {
        $this->soap = new \SoapClient($this->endpoint('TestAccess'));
        return $this->soap->TestAccess($this->merchantId, $this->apiKey);
    }

    public function getPaymentMethods()
    {
        $this->soap = new \SoapClient($this->endpoint('PaymentMethods'));
        return $this->soap->PaymentMethods($this->merchantId, $this->apiKey, 'pl');
    }

    public function getTransactionBySessionId($sessionId)
    {
        $this->soap = new \SoapClient($this->endpoint('GetTransactionBySessionId'));
        return $this->soap->TrnBySessionId($this->merchantId, $this->apiKey, $sessionId);
    }

    public function getTransactionFullBySessionId($sessionId)
    {
        $this->soap = new \SoapClient($this->endpoint('GetTransactionFullBySessionId'));
        return $this->soap->TrnFullBySessionId($this->merchantId, $this->apiKey, $sessionId);
    }

}