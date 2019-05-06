<?php

namespace NetborgTeam\P24;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class P24TransactionItem
 * @package NetborgTeam\P24
 *
 * @property int $id
 * @property string $p24_transaction_id
 * @property string $p24_name
 * @property string|null $p24_description
 * @property int $p24_quantity
 * @property int $p24_price
 * @property int|null $p24_number
 */
class P24TransactionItem extends Model
{
    /**
     * @var string
     */
    protected $table = "p24_transaction_items";

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $guarded = [];


    /**
     * @return BelongsTo
     */
    public function p24Transaction()
    {
        return $this->belongsTo(P24Transaction::class);
    }
}
