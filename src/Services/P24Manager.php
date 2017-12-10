<?php
namespace NetborgTeam\P24\Services;

use NetborgTeam\P24\Exceptions\InvalidMerchantIdException;
use NetborgTeam\P24\Exceptions\InvalidCRCException;
use NetborgTeam\P24\Exceptions\InvalidSenderException;
use NetborgTeam\P24\Exceptions\InvalidSignatureException;
use NetborgTeam\P24\Exceptions\P24ConnectionException;
use NetborgTeam\P24\Exceptions\InvalidTransactionException;
use NetborgTeam\P24\Exceptions\InvalidTransactionParameterException;
use NetborgTeam\P24\P24Transaction;
use NetborgTeam\P24\P24TransactionConfirmation;
use Illuminate\Http\Request;
use Curl\Curl;


/**
 * Description of P24Manager
 *
 * @author netborg
 */
class P24Manager {
    
    const ENDPOINT_LIVE = "https://secure.przelewy24.pl";
    const ENDPOINT_SANDBOX = "https://sandbox.przelewy24.pl";
    const PAYMENT_LIVE_REDIRECT_URL = "https://secure.przelewy24.pl/trnRequest/{token}";
    const PAYMENT_SANDBOX_REDIRECT_URL = "https://sandbox.przelewy24.pl/trnRequest/{token}";

    const API_VERSION = "3.2";
    
    
    private static $TRANSACTION_KEYS = [
        'p24_merchant_id',
        'p24_pos_id',
        'p24_session_id',
        'p24_amount',
        'p24_currency',
        'p24_description',
        'p24_email',
        'p24_client',
        'p24_address',
        'p24_zip',
        'p24_city',
        'p24_country',
        'p24_phone',
        'p24_language',
        'p24_method',
        'p24_url_return',
        'p24_url_status',
        'p24_time_limit',
        'p24_wait_for_result',
        'p24_channel',
        'p24_shipping',
        'p24_transfer_label',
        'p24_api_version',
        'p24_sign',
        'p24_encoding',
    ];
    
    private static $TRANSACTION_ITEM_KEYS = [
        'p24_name',
        'p24_description',
        'p24_quantity',
        'p24_price',
        'p24_number'
    ];
    
    private static $CONFIRMATION_KEYS = [
        'p24_merchant_id',
        'p24_pos_id',
        'p24_session_id',
        'p24_amount',
        'p24_currency',
        'p24_order_id',
        'p24_method',
        'p24_statement',
        'p24_sign'
    ];
    
    private static $CONFIRMATION_VERIFY_KEYS = [
        'p24_merchant_id',
        'p24_pos_id',
        'p24_session_id',
        'p24_amount',
        'p24_currency',
        'p24_order_id',
        'p24_sign'
    ];
    
    private static $ALLOWED_IPS = [
        '91.216.191.181',
        '91.216.191.182',
        '91.216.191.183',
        '91.216.191.184',
        '91.216.191.185'
    ];






    private $merchantId;
    private $posId;
    private $crc;
    private $endpoint;
    
    protected $data = [];
    
    
    
    public function __construct() 
    {
        $this->merchantId = config('p24.merchant_id', 0);
        $this->posId = config('p24.pos_id', 0) > 0 ? config('p24.pos_id', 0) : $this->merchantId;
        $this->crc = config('p24.crc', null);
        
        if ((int) $this->merchantId === 0) {
            throw new InvalidMerchantIdException();
        }
        if (!$this->crc) {
            throw new InvalidCRCException();
        }
        
        if (config('p24.mode') === 'live') {
            $this->endpoint = self::ENDPOINT_LIVE;
        } else {
            $this->endpoint = self::ENDPOINT_SANDBOX;
        }
    }
    
    
    public function __set($name, $value) {
        if (in_array($name, array_merge(
                static::$CONFIRMATION_KEYS, 
                static::$CONFIRMATION_VERIFY_KEYS,
                static::$TRANSACTION_KEYS))
        ) {
            $this->data[$name] = $value;
        }
    }
    
    public function __unset($name) {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
    }
    
    /**
     * Clears Transaction data.
     * 
     * @return $this
     */
    public function clear()
    {
        $this->data = [];
        return $this;
    }
    
    
    /**
     * Extracts and returns P24TransactionConfirmation from Request provided.
     * 
     * @param Request $request
     * @return P24TransactionConfirmation
     */
    public function parseTransactionConfirmation(Request $request)
    {
        return P24TransactionConfirmation::makeInstance($request, static::$CONFIRMATION_KEYS);
    }


    /**
     * Get P24WebServicesManager instance to execute requests to P24 Web Services.
     *
     * @return P24WebServicesManager|null
     */
    public function webServices()
    {
        return app()->make(P24WebServicesManager::class);
    }
    
    protected function parseVerificationResponse($response)
    {
        preg_match("/^error=(\d+)&errorMessage=(.*)$/", $response, $matches);
        if(count($matches) == 3) {
            $fields = [];
            foreach(explode('&', $matches[2]) as $error) {
                list($key, $message) = explode(':', trim($error));
                $fields[$key] = $message;
            }
            
            return [
                'error' => (int) $matches[1],
                'fields' => $fields
            ];
        }
        
        preg_match("/^error=(\d+)$/", $response, $matches);
        if (count($matches) == 2) {
            return [
                'error' => (int) $matches[1],
            ];
        }
        
        return false;
    }
    
    /**
     * Checks if request has been initiated from valid Przelewy24's server.
     * 
     * @param Request $request
     * @return boolean
     */
    public function isValidSender(Request $request)
    {
        return in_array($request->getClientIp(), static::$ALLOWED_IPS);
    }
    
    /**
     * Validates Transaction Confirmation received from Przelewy24 server.
     * On positive validation an object of P24TransactionConfirmation is being returned.
     * 
     * @param P24Transaction $transaction
     * @param Request|P24TransactionConfirmation $confirmation
     * @param string $sign 
     * @return P24TransactionConfirmation|null
     * @throws InvalidSenderException
     * @throws InvalidSignatureException
     * @throws InvalidTransactionParameterException
     */
    public function validateTransactionConfirmation(P24Transaction $transaction, $confirmation, $sign)
    {
        if ($confirmation instanceof Request) {
            $confirmation = $this->getTransactionConfirmationFromRequest($confirmation);
            $confirmation->p24Transaction()->associate($transaction);
        } 
        
        if ($confirmation instanceof P24TransactionConfirmation) {
            if (!$this->isValidTransactionConfirmationSignature($confirmation, $sign)) {
                $confirmation->verification_status = P24TransactionConfirmation::STATUS_INVALID_TRANSACTION_SIGNATURE;
                $confirmation->save();

                throw new InvalidSignatureException(
                        md5($this->makeTransactionConfirmationSignatureString($confirmation)), 
                        $sign);
            }
            
            $this->validateTransactionParameters($transaction, $confirmation);

            $confirmation->verification_status = P24TransactionConfirmation::STATUS_AWAITING_CONFIRMATION_VERIFICATION;
            $confirmation->save();

            return $confirmation;
        }
        
        return null;
    }
    
    protected function validateTransactionParameters(P24Transaction $transaction, P24TransactionConfirmation $confirmation)
    {
        if ($this->merchantId != $confirmation->p24_merchant_id) {
            $confirmation->verification_status = P24TransactionConfirmation::STATUS_INVALID_TRANSACTION_PARAMETER;
            $confirmation->save();
            throw new InvalidTransactionParameterException('p24_merchant_id', $this->merchantId, $confirmation->p24_merchant_id);
        }            
        if ($this->posId != $confirmation->p24_pos_id) {
            $confirmation->verification_status = P24TransactionConfirmation::STATUS_INVALID_TRANSACTION_PARAMETER;
            $confirmation->save();
            throw new InvalidTransactionParameterException('p24_pos_id', $this->posId, $confirmation->p24_pos_id);
        }
        if ($transaction->p24_amount != $confirmation->p24_amount) {
            $confirmation->verification_status = P24TransactionConfirmation::STATUS_INVALID_TRANSACTION_PARAMETER;
            $confirmation->save();
            throw new InvalidTransactionParameterException('p24_amount', $transaction->p24_amount, $confirmation->p24_amount);
        }
        if ($transaction->p24_currency != $confirmation->p24_currency) {
            $confirmation->verification_status = P24TransactionConfirmation::STATUS_INVALID_TRANSACTION_PARAMETER;
            $confirmation->save();
            throw new InvalidTransactionParameterException('p24_currency', $transaction->p24_currency, $confirmation->p24_currency);
        }
    }
    
    public function transaction(P24Transaction $transaction)
    {
        $this->signTransaction($transaction);
        $this->data = $this->serialize($transaction);
        
        return $this;
    }

    
    /**
     * 
     * @param P24Transaction $transaction
     * @return string|array|boolean Returns TOKEN (string) or FIELDS with errors (array) or FALSE on error
     * @throws InvalidTransactionException
     * @throws P24ConnectionException
     */
    public function register(P24Transaction $transaction=null)
    {
        if ($transaction) {
            $this->transaction($transaction);
        }
        
        if (count($this->data) == 0) {
            throw new InvalidTransactionException();
        }
        
        $this->p24_merchant_id = $this->merchantId;
        $this->p24_pos_id = $this->posId;
        $this->p24_api_version = self::API_VERSION;
        
        
        if (!isset($this->data['p24_url_status'])) {
            $this->data['p24_url_status'] = route('getTransactionStatusListener');
        }

        $curl = new Curl();
        $curl->post($this->endpoint.'/trnRegister', $this->data);
        
        if ($curl->error) {
            throw new P24ConnectionException($curl->errorMessage, $curl->errorCode);
        }
        
        $response = $this->parseRegistrationResponse($curl->rawResponse);
        
        if ($response && count($response) > 0) {
            if ($response['error'] === 0 && isset($response['token'])) {
                return $response['token'];
            } elseif ($response['error'] > 0) {
                return $response['fields'];
            }
        }
        
        return false;
    }
    
    /**
     * Sends back Transaction confirmation to PRZELEWY24's servers for verification.
     * Returns verification response.
     * 
     * @param P24TransactionConfirmation $confirmation
     * @return array
     * @throws P24ConnectionException
     */
    public function verifyTransactionConfirmation(P24TransactionConfirmation $confirmation)
    {
        $verify = collect($confirmation->toArray())
                ->only(self::$CONFIRMATION_VERIFY_KEYS)
                ->all();
        
        $curl = new Curl();
        $curl->post($this->endpoint.'/trnVerify', $verify);
        
        if ($curl->error) {
            throw new P24ConnectionException($curl->errorMessage, $curl->errorCode);
        }
        
        return $this->parseVerificationResponse($curl->response);
    }
    
    
    
    
    protected function signTransaction(P24Transaction $transaction)
    {
        $transaction->p24_sign = md5($this->makeTransactionSignatureString($transaction));
        if (!$transaction->id) {
            $transaction->save();
        }
        
        return $transaction->p24_sign;
    }
    
    
    protected function makeTransactionSignatureString(P24Transaction $transaction)
    {
        return implode('|', [
            $transaction->p24_session_id,
            $this->merchantId,
            $transaction->p24_amount,
            $transaction->p24_currency,
            $this->crc
        ]);
    }
    
    protected function makeTransactionConfirmationSignatureString(P24TransactionConfirmation $confirmation)
    {
        return implode('|', [
            $confirmation->p24_session_id,
            $confirmation->p24_order_id,
            $confirmation->p24_amount,
            $confirmation->p24_currency,
            $this->crc
        ]);
    }
    
    protected function isValidTransactionSignature(P24Transaction $transaction, $sign)
    {
        $expected = md5($this->makeTransactionSignatureString($transaction));
        return $expected === $sign;
    }
    
    protected function isValidTransactionConfirmationSignature(P24TransactionConfirmation $confirmation, $sign)
    {
        $expected = md5($this->makeTransactionConfirmationSignatureString($confirmation));
        return $expected === $sign;
    }
    
    protected function getTransactionConfirmationFromRequest(Request $request)
    {
        $confirmation = P24TransactionConfirmation::makeInstance($request, static::$CONFIRMATION_KEYS);
        $confirmation->save();

        if (!$this->isValidSender($request)) {
            $confirmation->verification_status = P24TransactionConfirmation::STATUS_INVALID_SENDER_IP;
            $confirmation->save();

            throw new InvalidSenderException($request->getClientIp());
        }
        
        return $confirmation;
    }
    
    protected function serialize(P24Transaction $transaction)
    {
        $params = collect($transaction->toArray())
                ->only(static::$TRANSACTION_KEYS)
                ->all();

        if ($transaction->p24TransactionItems->isNotEmpty()) {
            for($i=0; $i<$transaction->p24TransactionItems->count(); $i++) {
                $item = collect($transaction->p24TransactionItems[$i]->toArray())
                        ->only(static::$TRANSACTION_ITEM_KEYS)
                        ->all();
                
                foreach($item as $key => $value) {
                    $params[$key.'_'.$i] = $value;
                }
            }
        }
        
        return $params;
    }
    
    protected function parseRegistrationResponse($response)
    {
        preg_match("/^error=(\d+)&errorMessage=(.*)$/", $response, $matches);
        if(count($matches) == 3) {
            $fields = [];
            foreach(explode('&', $matches[2]) as $error) {
                list($key, $message) = explode(':', trim($error));
                $fields[$key] = $message;
            }
            
            return [
                'error' => (int) $matches[1],
                'fields' => $fields
            ];
        }
        
        preg_match("/^error=(\d+)&token=(.*)$/", $response, $matches);
        if (count($matches) == 3) {
            return [
                'error' => (int) $matches[1],
                'token' => $matches[2]
            ];
        }
        
        return false;
    }
}
