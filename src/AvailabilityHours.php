<?php
declare(strict_types=1);

namespace NetborgTeam\P24;

/**
 * Class AvailabilityHours
 * @package NetborgTeam\P24
 *
 * @property string|null $mondayToFriday
 * @property string|null $saturday
 * @property string|null $sunday
 */
class AvailabilityHours extends P24ResponseObject
{
    /**
     * @var string[]
     */
    protected $keys = [
        'mondayToFriday',
        'saturday',
        'sunday'
    ];
}
