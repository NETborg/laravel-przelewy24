<?php
declare(strict_types=1);

namespace NetborgTeam\P24\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use NetborgTeam\P24\Events\P24TransactionConfirmationSuccessEvent;
use NetborgTeam\P24\Exceptions\InvalidMerchantIdException;
use NetborgTeam\P24\Exceptions\InvalidCRCException;
use NetborgTeam\P24\Exceptions\InvalidSenderException;
use NetborgTeam\P24\Exceptions\InvalidSignatureException;
use NetborgTeam\P24\Exceptions\P24ConnectionException;
use NetborgTeam\P24\Exceptions\InvalidTransactionException;
use NetborgTeam\P24\Exceptions\InvalidTransactionParameterException;
use NetborgTeam\P24\GeneralError;
use NetborgTeam\P24\P24Transaction;
use NetborgTeam\P24\P24TransactionConfirmation;
use Illuminate\Http\Request;

/**
 * Description of P24Manager
 *
 * @author netborg
 */
class P24Manager
{
    const ENDPOINT_LIVE = "https://secure.przelewy24.pl";
    const ENDPOINT_SANDBOX = "https://sandbox.przelewy24.pl";
    const PAYMENT_LIVE_REDIRECT_URL = "https://secure.przelewy24.pl/trnRequest/{token}";
    const PAYMENT_SANDBOX_REDIRECT_URL = "https://sandbox.przelewy24.pl/trnRequest/{token}";

    const API_VERSION = "3.2";

    /**
     * @var String[]
     */
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

    /**
     * @var String[]
     */
    private static $TRANSACTION_ITEM_KEYS = [
        'p24_name',
        'p24_description',
        'p24_quantity',
        'p24_price',
        'p24_number'
    ];

    /**
     * @var String[]
     */
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

    /**
     * @var String[]
     */
    private static $CONFIRMATION_VERIFY_KEYS = [
        'p24_merchant_id',
        'p24_pos_id',
        'p24_session_id',
        'p24_amount',
        'p24_currency',
        'p24_order_id',
        'p24_sign'
    ];

    /**
     * @var String[]
     */
    private static $ALLOWED_IPS = [
        '91.216.191.181',
        '91.216.191.182',
        '91.216.191.183',
        '91.216.191.184',
        '91.216.191.185',
        '92.43.119.144',
        '92.43.119.145',
        '92.43.119.146',
        '92.43.119.147',
        '92.43.119.148',
        '92.43.119.149',
        '92.43.119.150',
        '92.43.119.151',
        '92.43.119.152',
        '92.43.119.153',
        '92.43.119.154',
        '92.43.119.155',
        '92.43.119.156',
        '92.43.119.157',
        '92.43.119.158',
        '92.43.119.159',
    ];

    /**
     * @var P24Signer
     */
    private $signer;

    private $transactionConfirmationValidator;


    /**
     * @var int
     */
    private $merchantId;

    /**
     * @var int
     */
    private $posId;

    /**
     * @var string
     */
    private $crc;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var Response|null
     */
    protected $testConnectionResponse;


    /**
     * P24Manager constructor.
     *
     * @param array                               $config
     * @param P24Signer                           $signer
     * @param P24TransactionConfirmationValidator $transactionConfirmationValidator
     *
     * @throws InvalidCRCException
     * @throws InvalidMerchantIdException
     * @throws P24ConnectionException
     */
    public function __construct(array $config, P24Signer $signer, P24TransactionConfirmationValidator $transactionConfirmationValidator)
    {
        $this->merchantId = (int) $config['merchant_id'] ?? 0;
        $this->posId = isset($config['pos_id']) ? (int) $config['pos_id'] ?? 0 : $this->merchantId;
        $this->crc = $config['crc'] ?? null;
        
        if (0 === $this->merchantId) {
            throw new InvalidMerchantIdException();
        }
        if (!$this->crc) {
            throw new InvalidCRCException();
        }
        
        if ('live' === $config['mode'] ?? 'sandbox') {
            $this->endpoint = self::ENDPOINT_LIVE;
        } else {
            $this->endpoint = self::ENDPOINT_SANDBOX;
        }

        $this->signer = $signer;
        $this->transactionConfirmationValidator = $transactionConfirmationValidator;

        $testConnectionResult = $this->testConnection();
        if ($testConnectionResult instanceof GeneralError) {
            throw new P24ConnectionException($testConnectionResult->errorMessage, $testConnectionResult->errorCode);
        }
    }
    
    
    public function __set($name, $value)
    {
        if (in_array($name, array_merge(
            static::$CONFIRMATION_KEYS,
            static::$CONFIRMATION_VERIFY_KEYS,
            static::$TRANSACTION_KEYS
        ))
        ) {
            $this->data[$name] = $value;
        }
    }
    
    public function __unset($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
    }

    /**
     * Tests connection to P24 server.
     *
     * @return bool
     */
    public function testConnection(): bool
    {
        $data = [
            'p24_merchant_id' => $this->merchantId,
            'p24_pos_id' => $this->posId,
            'p24_sign' => $this->signer->sign($this->makeTestConnectionPayload()),
        ];

        $client = new Client();
        $this->testConnectionResponse = $client->post($this->endpoint.'/testConnection', [ "form_params" => $data]);

        return $this->testConnectionResponse->getStatusCode() === 200
            && preg_match("/^error=0$/i", $this->testConnectionResponse->getBody()->getContents()) > 0;
    }

    /**
     * @return GeneralError|null
     */
    public function getConnectionError(): ?GeneralError
    {
        if ($this->testConnectionResponse instanceof Response) {
            return $this->parseErrorResponse($this->testConnectionResponse->getBody()->getContents());
        }

        return null;
    }


    /**
     * Clears Transaction data.
     *
     * @return self
     */
    public function clear(): self
    {
        $this->data = [];
        return $this;
    }
    

    /**
     * Extracts and returns P24TransactionConfirmation from Request provided.
     *
     * @param  Request                    $request
     * @return P24TransactionConfirmation
     */
    public function parseTransactionConfirmation(Request $request)
    {
        return P24TransactionConfirmation::makeInstance($request, static::$CONFIRMATION_KEYS);
    }


    /**
     * Get P24WebServicesManager instance to execute requests to P24 Web Services.
     *
     * @throws InvalidCRCException
     * @throws InvalidMerchantIdException
     * @throws P24ConnectionException
     * @throws \SoapFault
     *
     * @return P24WebServicesManager|null
     */
    public function webServices(): ?P24WebServicesManager
    {
        return app()->make(P24WebServicesManager::class);
    }

    /**
     * @param  string     $response
     * @return array|null
     */
    protected function parseVerificationResponse(string $response): ?array
    {
        preg_match("/^error=(\d+)&errorMessage=(.*)$/", $response, $matches);
        if (count($matches) == 3) {
            $fields = [];
            foreach (explode('&', $matches[2]) as $error) {
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
        
        return null;
    }
    
    /**
     * Checks if request has been initiated from valid Przelewy24's server.
     *
     * @param  Request $request
     * @return boolean
     */
    public function isValidSender(Request $request): bool
    {
        return in_array($request->getClientIp(), static::$ALLOWED_IPS);
    }

    /**
     * @param  P24Transaction $transaction
     * @return self
     */
    public function transaction(P24Transaction $transaction): self
    {
        $this->clear();

        $transaction->p24_merchant_id = $this->merchantId;
        $transaction->p24_pos_id = $this->posId;
        $transaction->p24_sign = $this->signer->sign($transaction->getSignablePayload());
        $transaction->save();

        $this->data = $this->serialize($transaction);
        
        return $this;
    }

    
    /**
     *
     * @param  P24Transaction              $transaction
     * @throws InvalidTransactionException
     * @throws P24ConnectionException
     * @return string|array|boolean        Returns TOKEN (string) or FIELDS with errors (array) or FALSE on error
     */
    public function register(P24Transaction $transaction=null)
    {
        if ($transaction instanceof P24Transaction) {
            $this->transaction($transaction);
        }
        
        if (count($this->data) == 0) {
            throw new InvalidTransactionException();
        }

        $this->p24_api_version = self::API_VERSION;

        if (!isset($this->data['p24_url_return'])) {
            $this->p24_url_return = url(route('getTransactionReturn', ['transactionId' => $transaction->id]), [], true);
        }
        
        if (!isset($this->data['p24_url_status'])) {
            $this->data['p24_url_status'] = url(route('getTransactionStatusListener'), [], true);
        }

        $client = new Client();
        $response = $client->post($this->endpoint.'/trnRegister', [ "form_params" => $this->data]);
        
        if ($response->getStatusCode() !== 200) {
            throw new P24ConnectionException($response->getReasonPhrase(), $response->getStatusCode());
        }
        
        $response = $this->parseRegistrationResponse($response->getBody()->getContents());
        
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
     * Validates Transaction Confirmation received from Przelewy24 server.
     * On positive validation an object of P24TransactionConfirmation is being returned.
     *
     * @param P24Transaction             $transaction
     * @param P24TransactionConfirmation $transactionConfirmation
     *
     * @throws InvalidSignatureException
     * @throws InvalidTransactionParameterException
     *
     * @return P24TransactionConfirmation
     */
    public function validateTransactionConfirmation(P24Transaction $transaction, P24TransactionConfirmation $transactionConfirmation): P24TransactionConfirmation
    {
        try {
            $this->transactionConfirmationValidator->validate($transaction, $transactionConfirmation);
        } catch (InvalidSignatureException $e) {
            $transactionConfirmation->verification_status = P24TransactionConfirmation::STATUS_INVALID_TRANSACTION_SIGNATURE;
            $transactionConfirmation->save();

            throw $e;
        } catch (InvalidTransactionParameterException $e) {
            $transactionConfirmation->verification_status = P24TransactionConfirmation::STATUS_INVALID_TRANSACTION_PARAMETER;
            $transactionConfirmation->save();

            throw $e;
        }

        $transactionConfirmation->verification_status = P24TransactionConfirmation::STATUS_VALID_UNVERIFIED;
        $transactionConfirmation->save();

        return $transactionConfirmation;
    }
    
    /**
     * Sends back Transaction confirmation to PRZELEWY24's servers for verification.
     * Returns verification response.
     *
     * @param  P24TransactionConfirmation $transactionConfirmation
     * @throws P24ConnectionException
     * @return P24TransactionConfirmation
     */
    public function verifyTransactionConfirmation(P24TransactionConfirmation $transactionConfirmation): P24TransactionConfirmation
    {
        $verify = collect($transactionConfirmation->toArray())
                ->only(self::$CONFIRMATION_VERIFY_KEYS)
                ->all();
        
        $client = new Client();
        $response = $client->post($this->endpoint.'/trnVerify', ['form_params' => $verify]);

        if ($response->getStatusCode() !== 200) {
            throw new P24ConnectionException($response->getReasonPhrase(), $response->getStatusCode());
        }

        $responseContent = $response->getBody()->getContents();
        $verificationResult = $this->parseVerificationResponse($responseContent);

        if (isset($verificationResult['error']) && $verificationResult['error'] === 0) {
            $transactionConfirmation->setVerificationResult(
                P24TransactionConfirmation::STATUS_VERIFIED,
                $responseContent
            );
            return $transactionConfirmation;
        }

        $transactionConfirmation->setVerificationResult(
            P24TransactionConfirmation::STATUS_VERIFICATION_FAILED,
            $responseContent
        );
        return $transactionConfirmation;
    }

    /**
     * @return array
     */
    protected function makeTestConnectionPayload(): array
    {
        return [
            $this->posId,
            $this->crc
        ];
    }

    /**
     * @param  P24Transaction $transaction
     * @return array
     */
    public function serialize(P24Transaction $transaction)
    {
        $params = collect($transaction->toArray())
                ->only(static::$TRANSACTION_KEYS)
                ->all();

        if ($transaction->p24TransactionItems->isNotEmpty()) {
            for ($i=0; $i<$transaction->p24TransactionItems->count(); $i++) {
                $item = collect($transaction->p24TransactionItems[$i]->toArray())
                        ->only(static::$TRANSACTION_ITEM_KEYS)
                        ->all();
                
                foreach ($item as $key => $value) {
                    $params[$key.'_'.$i] = $value;
                }
            }
        }
        
        return $params;
    }

    /**
     * @param $response
     * @return GeneralError
     */
    public function parseErrorResponse($response)
    {
        preg_match("/^error=(\d+)&errorMessage=(.*)$/", $response, $matches);
        if (count($matches) == 3) {
            return new GeneralError([
                'errorCode' => (int) $matches[1],
                'errorMessage' => $matches[2]
            ]);
        }

        return new GeneralError([
            'errorCode' => -1,
            'errorMessage' => "Unable to parse response string: `$response`",
        ]);
    }

    /**
     * @param $response
     * @return array|bool
     */
    protected function parseRegistrationResponse($response)
    {
        preg_match("/^error=(\d+)&errorMessage=(.*)$/", $response, $matches);
        if (count($matches) == 3) {
            $fields = [];
            foreach (explode('&', $matches[2]) as $error) {
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
