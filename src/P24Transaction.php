<?php

namespace NetborgTeam\P24;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use NetborgTeam\P24\Services\P24Manager;
use NetborgTeam\P24\Supporters\RandGenerator;
use Ramsey\Uuid\Uuid;

/**
 * Class P24Transaction
 * @package NetborgTeam\P24
 *
 * @property string $id
 * @property string $p24_session_id
 * @property int $p24_amount
 * @property string $p24_currency
 * @property string|null $p24_description
 * @property string $p24_email
 * @property string|null $p24_client
 * @property string|null $p24_address
 * @property string|null $p24_zip
 * @property string|null $p24_city
 * @property string|null $p24_country
 * @property string|null $p24_phone
 * @property string|null $p24_language
 * @property int|null $p24_method
 * @property string|null $p24_url_return
 * @property string|null $p24_url_status
 * @property int|null $p24_time_limit
 * @property int|null $p24_wait_for_result
 * @property int|null $p24_channel
 * @property int|null $p24_shipping
 * @property string|null $p24_transfer_label
 * @property string $p24_sign
 * @property string $p24_encoding
 * @property int|null $p24_order_id
 * @property string|null $p24_statement
 * @property string|null $token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class P24Transaction extends Model
{
    protected $table = "p24_transactions";
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $guarded = [
        'token',
        'created_at',
        'updated_at',
    ];

    protected $dates = ['created_at', 'updated_at'];

    protected $casts = [
        'p24_amount' => 'integer',
        'p24_method' => 'integer',
        'p24_time_limit' => 'integer',
        'p24_wait_for_result' => 'integer',
        'p24_channel' => 'integer',
        'p24_shipping' => 'integer',
        'p24_order_id' => 'integer',
    ];


    /**
     * Generates UUID for Transaction.
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
                "[P24Transaction::generateUid()]: Unable to generate P24Transaction's UUID due to: "
                .$e->getMessage()
            );

            return Str::random($chars);
        }
    }
    
    
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
        return 'live' === config('p24.mode')
            ? redirect(str_replace('{token}', $this->token, P24Manager::PAYMENT_LIVE_REDIRECT_URL))
            : redirect(str_replace('{token}', $this->token, P24Manager::PAYMENT_SANDBOX_REDIRECT_URL));
    }
}
