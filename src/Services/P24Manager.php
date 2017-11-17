<?php
namespace NetborgTeam\P24\Services;

use NetborgTeam\P24\Exceptions\InvalidMerchantIdException;
use NetborgTeam\P24\Exceptions\InvalidCRCException;
use NetborgTeam\P24\Exceptions\InvalidSenderException;
use NetborgTeam\P24\Exceptions\InvalidSignatureException;
use NetborgTeam\P24\P24Transaction;
use NetborgTeam\P24\P24TransactionConfirmation;
use Illuminate\Http\Request;


/**
 * Description of P24Manager
 *
 * @author netborg
 */
class P24Manager {
    
    const ENDPOINT_LIVE = "https://secure.przelewy24.pl/";
    const ENDPOINT_SANDBOX = "https://sandbox.przelewy24.pl/";
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
        'p24_statement',
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
    
    
    
    public function __construct() {
        $this->posId = config('p24.pos_id', 0);
        $this->merchantId = config('p24.merchant_id', 0) > 0 ? config('p24.merchant_id', 0) : $this->posId;
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
     * @return P24TransactionConfirmation|null
     * @throws InvalidSenderException
     * @throws InvalidSignatureException
     */
    public function validateTransactionConfirmation(P24Transaction $transaction, $confirmation)
    {
        if ($confirmation instanceof Request) {
            $confirmation = $this->getTransactionConfirmationFromRequest($confirmation);
            $confirmation->p24Transaction()->associate($transaction);
        } 
        
        if ($confirmation instanceof P24TransactionConfirmation) {
            if (!$this->isValidTransactionConfirmationSignature($confirmation, $request->input('p24_sign'))) {
                $confirmation->verification_status = P24TransactionConfirmation::STATUS_INVALID_TRANSACTION_SIGNATURE;
                $confirmation->save();

                throw new InvalidSignatureException(
                        md5($this->makeTransactionConfirmationSignatureString($confirmation)), 
                        $request->input('p24_sign'));
            }

            $confirmation->verification_status = P24TransactionConfirmation::STATUS_AWAITING_CONFIRMATION_VERIFICATION;
            $confirmation->save();

            return $confirmation;
        }
        
        return null;
    }
    
    
    public function registerTransaction(P24Transaction $transaction)
    {
        throw new \Exception("TODO - create registerTransaction() procedure.");
    }
    
    public function verifyTransactionConfirmation(P24TransactionConfirmation $confirmation)
    {
        throw new \Exception("TODO - create verifyTransactionConfirmation() procedure.");
    }
    
    
    
    
    protected function signTransaction(P24Transaction $transaction)
    {
        $transaction->p24_sign = md5($this->makeTransactionSignatureString($transaction));
        $transaction->save();
        
        return $this;
    }
    
    
    protected function makeTransactionSignatureString(P24Transaction $transaction)
    {
        return implode('|', [
            $transaction->p24_session_id,
            $transaction->p24_merchant_id,
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
}
