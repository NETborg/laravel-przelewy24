<?php
declare(strict_types=1);

namespace NetborgTeam\P24\Contracts;

interface P24SignerContract
{
    /**
     * Sign payload string.
     *
     * @param  P24SignableContract|array $signable
     * @return string
     */
    public function sign($signable): string;
}
