<?php
declare(strict_types=1);

namespace NetborgTeam\P24\Contracts;

interface P24SignableContract
{

    /**
     * Creates and returns signable attributes array.
     *
     * @return array
     */
    public function getSignablePayload(): array;
}
