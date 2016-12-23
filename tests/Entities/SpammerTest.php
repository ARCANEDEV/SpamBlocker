<?php namespace Arcanedev\SpamBlocker\Tests\Entities;

use Arcanedev\SpamBlocker\Entities\Spammer;
use Arcanedev\SpamBlocker\Tests\TestCase;

/**
 * Class     SpammerTest
 *
 * @package  Arcanedev\SpamBlocker\Tests\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SpammerTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    /** @test */
    public function it_can_be_instantiated()
    {
        $spammer = $this->createBadSpammer();

        $this->assertSame('http://bad-spammer.xyz', $spammer->host());
        $this->assertTrue($spammer->isBlocked());
    }

    /** @test */
    public function it_can_convert_to_array()
    {
        $spammer = $this->createBadSpammer();

        $this->assertInstanceOf(\Illuminate\Contracts\Support\Arrayable::class, $spammer);

        $array = $spammer->toArray();

        $this->assertInternalType('array', $array);
        $this->assertArrayHasKey('host', $array);
        $this->assertArrayHasKey('blocked', $array);
        $this->assertEquals($spammer->host(), $array['host']);
        $this->assertEquals($spammer->isBlocked(), $array['blocked']);
    }

    /** @test */
    public function it_can_convert_to_json()
    {
        $spammer = $this->createBadSpammer();

        $this->assertInstanceOf(\Illuminate\Contracts\Support\Jsonable::class, $spammer);

        $json = $spammer->toJson();

        $this->assertJson($json);
    }

    /** @test */
    public function it_can_make()
    {
        $spammer = Spammer::make($url = 'http://google.com', false);

        $this->assertSame($url, $spammer->host());
        $this->assertFalse($spammer->isBlocked());
    }

    /** @test */
    public function it_can_check_if_has_same_host_url()
    {
        $spammer = $this->createBadSpammer();

        $this->assertTrue($spammer->isSameHost('http://bad-spammer.xyz'));
        $this->assertFalse($spammer->isSameHost('http://google.com'));
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    private function createBadSpammer()
    {
        return new Spammer($url = 'http://bad-spammer.xyz', true);
    }
}
