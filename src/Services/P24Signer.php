<?php
declare(strict_types=1);

namespace NetborgTeam\P24\Services;

use NetborgTeam\P24\Contracts\P24SignableContract;
use NetborgTeam\P24\Contracts\P24SignerContract;

class P24Signer implements P24SignerContract
{

    /**
     * @var string
     */
    private $crc;


    /**
     * P24TransactionSigner constructor.
     * @param string $crc
     */
    public function __construct(string $crc)
    {
        $this->crc = $crc;
    }


    /**
     * Sign payload string.
     *
     * @param  P24SignableContract|array $signable
     * @return string
     */
    public function sign($signable): string
    {
        if (!($signable instanceof P24SignableContract) && !is_array($signable)) {
            throw new \InvalidArgumentException("Signable parameter must implement `".P24SignableContract::class."` or to be an array.");
        }

        $payload = $signable instanceof P24SignableContract
            ? $signable->getSignablePayload()
            : $signable;

        $payload[] = $this->crc;

        return md5(implode('|', $payload));
    }
}
