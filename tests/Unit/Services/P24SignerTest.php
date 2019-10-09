<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use NetborgTeam\P24\Contracts\P24SignableContract;
use NetborgTeam\P24\Services\P24Signer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \NetborgTeam\P24\Services\P24Signer
 */
class P24SignerTest extends TestCase
{
    /**
     * @var string
     */
    protected $crc;


    protected function setUp(): void
    {
        parent::setUp();

        $this->crc = 'a4b5f1c3e2db2010d';
    }

    /**
     * @param $payload
     * @param string $correctSignature
     *
     * @dataProvider providePayloads
     */
    public function testSignature($payload, string $correctSignature): void
    {
        $signer = new P24Signer($this->crc);

        $signature = $signer->sign($payload);

        $this->assertSame($correctSignature, $signature);
    }


    /**
     * @return \Generator
     */
    public function providePayloads(): \Generator
    {
        yield [['testArgument1'], '30b348921d1402e4d58167b6a4004a1f'];
        yield [['testArgument1', 'testArgument2'], '0da7b60b608a95ea294ee4ac4698ea4f'];
        yield [$this->makeSignableInstance(['testArgument1']), '30b348921d1402e4d58167b6a4004a1f'];
        yield [$this->makeSignableInstance(['testArgument1', 'testArgument2']), '0da7b60b608a95ea294ee4ac4698ea4f'];
    }

    /**
     * @param  array               $payload
     * @return P24SignableContract
     */
    protected function makeSignableInstance(array $payload): P24SignableContract
    {
        $signable = $this->prophesize(P24SignableContract::class);
        $signable
            ->getSignablePayload()
            ->willReturn($payload);

        return $signable->reveal();
    }
}
