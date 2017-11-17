<?php

namespace NetborgTeam\P24;

use Illuminate\Database\Eloquent\Model;

class P24Transaction extends Model
{
    protected $table = "p24_transactions";
    
    protected $guarded = [
        'token',
        'created_at', 
        'updated_at', 
    ];
    
    
    
    public function p24TransactionItems()
    {
        return $this->hasMany(P24TransactionItem::class);
    }
    
    public function p24TransactionConfirmation()
    {
        return $this->hasOne(P24TransactionConfirmation::class);
    }
    
    
    
    public function token($token)
    {
        $this->token = $token;
        $this->save();
        
        return $this;
    }
}
