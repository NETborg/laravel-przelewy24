<?php
declare(strict_types=1);

namespace Tests\Unit;


use NetborgTeam\P24\AvailabilityHours;
use PHPUnit\Framework\TestCase;

class AvailabilityHoursTest extends TestCase
{
    /**
     * @var AvailabilityHours
     */
    private $availabilityHours;

    /**
     * @var \stdClass
     */
    private $response;




    /**
     * @var string|null
     */
    private $mondayToFriday;

    /**
     * @var string|null
     */
    private $saturday;

    /**
     * @var string|null
     */
    private $sunday;





    protected function setUp(): void
    {
        $this->mondayToFriday = "00-24";
        $this->saturday = "10-18";
        $this->sunday = "unavailable";

        $this->response = new \stdClass();
        $this->response->mondayToFriday = $this->mondayToFriday;
        $this->response->saturday = $this->saturday;
        $this->response->sunday = $this->sunday;

        $this->availabilityHours = new AvailabilityHours($this->response);
    }


    public function testGetters()
    {
        $this->assertSame($this->mondayToFriday, $this->availabilityHours->mondayToFriday);
        $this->assertSame($this->saturday, $this->availabilityHours->saturday);
        $this->assertSame($this->sunday, $this->availabilityHours->sunday);
    }

    public function testSetters()
    {
        $response = new \stdClass();
        $response->mondayToFriday = "unavailable";
        $response->saturday = "unavailable";
        $response->sunday = "unavailable";

        $availabilityHours = new AvailabilityHours($response);

        $availabilityHours->mondayToFriday = $this->mondayToFriday;
        $availabilityHours->saturday = $this->saturday;
        $availabilityHours->sunday = $this->sunday;


        $this->assertSame($this->mondayToFriday, $availabilityHours->mondayToFriday);
        $this->assertSame($this->saturday, $availabilityHours->saturday);
        $this->assertSame($this->sunday, $availabilityHours->sunday);
    }

    public function testGetResponse()
    {
        $this->assertSame(
            $this->response,
            $this->availabilityHours->getResponse(),
            "Method getResponse() doesn't return right value."
        );
    }

    public function testToArray()
    {
        $this->assertIsArray($this->availabilityHours->toArray());

        $this->assertSame([
            'mondayToFriday' => $this->mondayToFriday,
            'saturday' => $this->saturday,
            'sunday' => $this->sunday,
        ], $this->availabilityHours->toArray());
    }

    public function testToJson()
    {
        $this->assertJson($this->availabilityHours->toJson());

        $this->assertJsonStringEqualsJsonString(json_encode([
            'mondayToFriday' => $this->mondayToFriday,
            'saturday' => $this->saturday,
            'sunday' => $this->sunday,
        ]), $this->availabilityHours->toJson());

        $this->assertJsonStringEqualsJsonString(json_encode([
            'mondayToFriday' => $this->mondayToFriday,
            'saturday' => $this->saturday,
            'sunday' => $this->sunday,
        ], JSON_PRETTY_PRINT), $this->availabilityHours->toJson(JSON_PRETTY_PRINT));
    }

}
