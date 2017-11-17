<?php

namespace NetborgTeam\P24;

use Illuminate\Database\Eloquent\Model;

class P24TransactionItem extends Model
{
    protected $table = "p24_transaction_items";
    public $timestamps = false;
    
    protected $guarded = [];
    
    
    
    
    public function p24Transaction()
    {
        return $this->belongsTo(P24Transaction::class);
    }
}
