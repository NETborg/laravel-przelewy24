<?php
declare(strict_types=1);

namespace NetborgTeam\P24;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use NetborgTeam\P24\Contracts\P24SignableContract;
use Ramsey\Uuid\Uuid;

/**
 * Class P24TransactionConfirmation
 * @package NetborgTeam\P24
 *
 * @property string $id
 * @property string $p24_transaction_id
 * @property int $p24_merchant_id
 * @property int $p24_pos_id
 * @property string $p24_session_id
 * @property int $p24_amount
 * @property string $p24_currency
 * @property int $p24_order_id
 * @property int $p24_method
 * @property string|null $p24_statement
 * @property string $p24_sign
 * @property string $verification_status
 * @property string|null verification_sign
 * @property Carbon|null $verified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class P24TransactionConfirmation extends Model implements P24SignableContract
{
    const STATUS_NEW = "new";
    const STATUS_INVALID_TRANSACTION_SIGNATURE = "invalid_transaction_signature";
    const STATUS_INVALID_VERIFICATION_SIGNATURE = "invalid_verification_signature";
    const STATUS_INVALID_TRANSACTION_PARAMETER = "invalid_transaction_parameter";
    const STATUS_INVALID_SENDER_IP = "invalid_sender_ip";
    const STATUS_VALID_UNVERIFIED = "valid_unverified";
    const STATUS_VERIFIED = "verified";
    const STATUS_VERIFICATION_FAILED = "verification_failed";


    /**
     * @var string
     */
    protected $table = "p24_transaction_confirmations";

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var array
     */
    protected $dates = [ 'verified_at', 'created_at', 'updated_at' ];

    /**
     * @var array
     */
    protected $guarded = [
        'verification_status',
        'verification_sign',
        'verified_at',
        'created_at',
        'updated_at'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'p24_merchant_id' => 'integer',
        'p24_pos_id' => 'integer',
        'p24_amount' => 'integer',
        'p24_order_id' => 'integer',
        'p24_method' => 'integer',
    ];
    
    
    
    
    /**
     * Builds a P24TransactionConfirmation object from Request.
     *
     * @param  Request                    $request
     * @param  array                      $keys
     * @return P24TransactionConfirmation
     */
    public static function makeInstance(Request $request, array $keys): P24TransactionConfirmation
    {
        $confirmation = new P24TransactionConfirmation();
        
        foreach ($keys as $key) {
            if ($value = $request->input($key, null)) {
                $confirmation->{$key} = $value;
            }
        }
        
        return $confirmation;
    }

    /**
     * Generates UUID for Transaction Confirmation.
     *
     * @param  int    $chars
     * @return string
     */
    public static function generateUid($chars = 36): string
    {
        try {
            return (string) Uuid::uuid4();
        } catch (\Exception $e) {
            Log::error(
                "[P24TransactionConfirmation::generateUid()]: Unable to generate P24TransactionConfirmation's UUID due to: "
                .$e->getMessage()
            );

            return Str::random($chars);
        }
    }


    /**
     * @return BelongsTo
     */
    public function p24Transaction()
    {
        return $this->belongsTo(P24Transaction::class);
    }


    /**
     * Confirms positive verification of this Transaction Confirmation.
     *
     * @param  string      $status
     * @param  string|null $sign
     * @return self
     */
    public function setVerificationResult(string $status, ?string $sign): self
    {
        $this->verification_sign = $sign;
        $this->verified_at = Carbon::now();
        $this->verification_status = $status;
        
        $this->save();
        
        return $this;
    }

    /**
     * Creates and returns signable attributes array.
     *
     * @return array
     */
    public function getSignablePayload(): array
    {
        return [
            $this->p24_session_id,
            $this->p24_order_id,
            $this->p24_amount,
            $this->p24_currency
        ];
    }
}
