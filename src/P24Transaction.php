<?php

namespace NetborgTeam\P24;

use Illuminate\Database\Eloquent\Model;
use NetborgTeam\P24\Supporters\RandGenerator;

class P24Transaction extends Model
{
    protected $table = "p24_transactions";
    
    protected $guarded = [
        'token',
        'created_at', 
        'updated_at', 
    ];
    
    
    public static function makeUniqueId($sessionId, $seperator='|')
    {
        do {
            $uniqueId = $sessionId.$seperator.RandGenerator::generate(100 - strlen($seperator.$sessionId));
            $check = P24Transaction::where('p24_session_id', $uniqueId)->first();
        } while ($check);
        
        return $uniqueId;
    }
    
    
    
    public function p24TransactionItems()
    {
        return $this->hasMany(P24TransactionItem::class);
    }
    
    public function p24TransactionConfirmation()
    {
        return $this->hasOne(P24TransactionConfirmation::class);
    }
    
    
    
    public function token($token=null)
    {
        if ($token) {
            $this->token = $token;
            $this->save();

            return $this;
        }
        return $this->token;
    }

    public function redirectForPayment()
    {
        return config('p24.mode') == 'live'
            ? redirect('https://secure.przelewy24.pl/trnRequest/'.$this->token)
            : redirect('https://sandbox.przelewy24.pl/trnRequest/'.$this->token);
    }
}
