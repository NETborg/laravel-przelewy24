<?php

namespace NetborgTeam\P24;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
    protected $dates = [ 'verified_at' ];
    
    protected $guarded = [
        'verification_status',
        'verification_sign',
        'verified_at',
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
