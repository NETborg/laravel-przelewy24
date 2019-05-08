<?php
declare(strict_types=1);

namespace Tests\Unit;


use NetborgTeam\P24\GeneralError;
use PHPUnit\Framework\TestCase;

class GeneralErrorTest extends TestCase
{

    const ERROR_CODE = 5;
    const ERROR_MESSAGE = "Default message.";

    /**
     * @var GeneralError
     */
    private $error;


    protected function setUp(): void
    {
        $this->error = new GeneralError([
            'errorCode' => self::ERROR_CODE,
            'errorMessage' => self::ERROR_MESSAGE,
        ]);
    }

    public function testGetters()
    {
        $this->assertSame(self::ERROR_CODE, $this->error->errorCode);
        $this->assertSame(self::ERROR_MESSAGE, $this->error->errorMessage);
    }

    public function testSetters()
    {
        $error = new GeneralError([
            'errorCode' => 1,
            'errorMessage' => "Different error message",
        ]);

        $error->errorCode = self::ERROR_CODE;
        $error->errorMessage = self::ERROR_MESSAGE;

        $this->assertSame(self::ERROR_CODE, $error->errorCode);
        $this->assertSame(self::ERROR_MESSAGE, $error->errorMessage);
    }

    public function testGetResponse()
    {
        $this->assertSame([
            'errorCode' => self::ERROR_CODE,
            'errorMessage' => self::ERROR_MESSAGE,
        ], $this->error->getResponse(), "Method getResponse() doesn't return right value.");
    }

    public function testToArray()
    {
        $this->assertIsArray($this->error->toArray());

        $this->assertSame([
            'errorCode' => self::ERROR_CODE,
            'errorMessage' => self::ERROR_MESSAGE,
        ], $this->error->toArray());
    }

    public function testToJson()
    {
        $this->assertJson($this->error->toJson());

        $this->assertJsonStringEqualsJsonString(json_encode([
            'errorCode' => self::ERROR_CODE,
            'errorMessage' => self::ERROR_MESSAGE,
        ]), $this->error->toJson());

        $this->assertJsonStringEqualsJsonString(json_encode([
            'errorCode' => self::ERROR_CODE,
            'errorMessage' => self::ERROR_MESSAGE,
        ], JSON_PRETTY_PRINT), $this->error->toJson(JSON_PRETTY_PRINT));
    }

}
