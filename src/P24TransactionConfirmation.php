<?php

namespace NetborgTeam\P24;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
class P24TransactionConfirmation extends Model
{
    const STATUS_NEW = "new";
    const STATUS_INVALID_TRANSACTION_SIGNATURE = "invalid_transaction_signature";
    const STATUS_AWAITING_CONFIRMATION_VERIFICATION = "awaiting_confirmation_verification";
    const STATUS_INVALID_VERIFICATION_SIGNATURE = "invalid_verification_signature";
    const STATUS_INVALID_TRANSACTION_PARAMETER = "invalid_transaction_parameter";
    const STATUS_INVALID_SENDER_IP = "invalid_sender_ip";
    const STATUS_CONFIRMED = "confirmed";
    const STATUS_CONFIRMED_VERIFIED = "confirmed_verified";
    
    
    
    
    protected $table = "p24_transaction_confirmations";
    public $incrementing = false;
    protected $keyType = 'string';

    protected $dates = [ 'verified_at', 'created_at', 'updated_at' ];
    
    protected $guarded = [
        'verification_status',
        'verification_sign',
        'verified_at',
        'created_at',
        'updated_at'
    ];
    
    
    
    
    /**
     * Builds a P24TransactionConfirmation object from Request.
     *
     * @param  Request                                     $request
     * @param  array                                       $keys
     * @return \NetborgTeam\P24\P24TransactionConfirmation
     */
    public static function makeInstance(Request $request, array $keys)
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
    public static function generateUid($chars = 36)
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
    
    
    
    
    
    public function p24Transaction()
    {
        return $this->belongsTo(P24Transaction::class);
    }
    
    
    /**
     * Confirms positive verification of this Transaction Confirmation.
     *
     * @param  string $sign
     * @return $this
     */
    public function confirmVerified($sign)
    {
        $this->verification_sign = $sign;
        $this->verified_at = Carbon::now();
        
        $this->save();
        
        return $this;
    }
}
