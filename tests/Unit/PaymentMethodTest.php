<?php
declare(strict_types=1);

namespace Tests\Unit;


use NetborgTeam\P24\AvailabilityHours;
use NetborgTeam\P24\PaymentMethod;
use PHPUnit\Framework\TestCase;

class PaymentMethodTest extends TestCase
{

    /**
     * @var PaymentMethod
     */
    private $paymentMethod;

    /**
     * @var \stdClass
     */
    private $availabilityHours;

    /**
     * @var \stdClass
     */
    private $response;



    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $status;

    /**
     * @var AvailabilityHours
     */
    private $avaibilityHours;



    protected function setUp(): void
    {
        $this->availabilityHours = new \stdClass();
        $this->availabilityHours->mondayToFriday = "00-24";
        $this->availabilityHours->saturday = "10-18";
        $this->availabilityHours->sunday = "unavailable";

        $this->id = 1000;
        $this->name = "Test Payment Method";
        $this->status = true;
        $this->avaibilityHours = new AvailabilityHours($this->availabilityHours);

        $this->response = new \stdClass();
        $this->response->id = $this->id;
        $this->response->name = $this->name;
        $this->response->status = $this->status;
        $this->response->avaibilityHours = $this->availabilityHours;

        $this->paymentMethod = new PaymentMethod($this->response);
    }


    public function testGetters()
    {
        $this->assertSame($this->id, $this->paymentMethod->id);
        $this->assertSame($this->name, $this->paymentMethod->name);
        $this->assertSame($this->status, $this->paymentMethod->status);
        $this->assertSame($this->avaibilityHours->toArray(), $this->paymentMethod->avaibilityHours->toArray());
    }

    public function testSetters()
    {
        $availabilityHours = new \stdClass();
        $availabilityHours->mondayToFriday = "unavailable";
        $availabilityHours->saturday = "unavailable";
        $availabilityHours->sunday = "unavailable";

        $paymentMethod = new PaymentMethod([
            'id' => 5,
            'name' => "Another Test Payment Method",
            'status' => false,
            'avaibilityHours' => $availabilityHours
        ]);

        $paymentMethod->id = $this->id;
        $paymentMethod->name = $this->name;
        $paymentMethod->status = $this->status;
        $paymentMethod->avaibilityHours = $this->avaibilityHours;


        $this->assertSame($this->id, $paymentMethod->id);
        $this->assertSame($this->name, $paymentMethod->name);
        $this->assertSame($this->status, $paymentMethod->status);
        $this->assertSame($this->avaibilityHours->toArray(), $paymentMethod->avaibilityHours->toArray());
    }

    public function testGetResponse()
    {
        $this->assertSame(
            $this->response,
            $this->paymentMethod->getResponse(),
            "Method getResponse() doesn't return right value."
        );
    }

    public function testToArray()
    {
        $this->assertIsArray($this->paymentMethod->toArray());

        $this->assertSame([
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'avaibilityHours' => $this->avaibilityHours->toArray(),
        ], $this->paymentMethod->toArray());
    }

    public function testToJson()
    {
        $this->assertJson($this->paymentMethod->toJson());

        $this->assertJsonStringEqualsJsonString(json_encode([
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'avaibilityHours' => $this->avaibilityHours->toArray(),
        ]), $this->paymentMethod->toJson());

        $this->assertJsonStringEqualsJsonString(json_encode([
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'avaibilityHours' => $this->avaibilityHours->toArray(),
        ], JSON_PRETTY_PRINT), $this->paymentMethod->toJson(JSON_PRETTY_PRINT));
    }

}
