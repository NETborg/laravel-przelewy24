<?php
declare(strict_types=1);

namespace NetborgTeam\P24;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use NetborgTeam\P24\Contracts\P24SignableContract;
use NetborgTeam\P24\Services\P24Manager;
use NetborgTeam\P24\Supporters\RandGenerator;
use Ramsey\Uuid\Uuid;

/**
 * Class P24Transaction
 * @package NetborgTeam\P24
 *
 * @property string $id
 * @property int $p24_merchant_id
 * @property int $p24_pos_id
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
 * @property string|null $p24_sign
 * @property string $p24_encoding
 * @property int|null $p24_order_id
 * @property string|null $p24_statement
 * @property string|null $token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class P24Transaction extends Model implements P24SignableContract
{
    /**
     * @var string
     */
    protected $table = "p24_transactions";

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
    protected $guarded = [
        'token',
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * @var array
     */
    protected $casts = [
        'p24_merchant_id' => 'integer',
        'p24_pos_id' => 'integer',
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
    public static function generateUid($chars = 36): string
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

    /**
     * @param $sessionId
     * @param  string $seperator
     * @return string
     */
    public static function makeUniqueId($sessionId, $seperator='|'): string
    {
        do {
            $uniqueId = $sessionId.$seperator.RandGenerator::generate(100 - strlen($seperator.$sessionId));
            $check = P24Transaction::where('p24_session_id', $uniqueId)->first();
        } while ($check);
        
        return $uniqueId;
    }


    /**
     * @return HasMany
     */
    public function p24TransactionItems()
    {
        return $this->hasMany(P24TransactionItem::class);
    }

    /**
     * @return HasOne
     */
    public function p24TransactionConfirmation()
    {
        return $this->hasOne(P24TransactionConfirmation::class);
    }


    /**
     * @param  string|null       $token
     * @return $this|string|null
     */
    public function token(string $token=null)
    {
        if ($token) {
            $this->token = $token;
            $this->save();

            return $this;
        }
        return $this->token;
    }

    /**
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function redirectForPayment()
    {
        return 'live' === config('p24.mode')
            ? redirect(str_replace('{token}', $this->token, P24Manager::PAYMENT_LIVE_REDIRECT_URL))
            : redirect(str_replace('{token}', $this->token, P24Manager::PAYMENT_SANDBOX_REDIRECT_URL));
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
            $this->p24_merchant_id,
            $this->p24_amount,
            $this->p24_currency,
        ];
    }
}
