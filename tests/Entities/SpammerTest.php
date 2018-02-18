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
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated()
    {
        $spammer = $this->createBadSpammer();

        static::assertSame('http://bad-spammer.xyz', $spammer->host());
        static::assertTrue($spammer->isBlocked());
    }

    /** @test */
    public function it_can_convert_to_array()
    {
        $spammer = $this->createBadSpammer();

        static::assertInstanceOf(\Illuminate\Contracts\Support\Arrayable::class, $spammer);

        $array = $spammer->toArray();

        static::assertInternalType('array', $array);
        static::assertArrayHasKey('host', $array);
        static::assertArrayHasKey('blocked', $array);
        static::assertEquals($spammer->host(), $array['host']);
        static::assertEquals($spammer->isBlocked(), $array['blocked']);
    }

    /** @test */
    public function it_can_convert_to_json()
    {
        $spammer = $this->createBadSpammer();

        static::assertInstanceOf(\Illuminate\Contracts\Support\Jsonable::class, $spammer);

        $json = $spammer->toJson();

        static::assertJson($json);
    }

    /** @test */
    public function it_can_make()
    {
        $spammer = Spammer::make($url = 'http://google.com', false);

        static::assertSame($url, $spammer->host());
        static::assertFalse($spammer->isBlocked());
    }

    /** @test */
    public function it_can_check_if_has_same_host_url()
    {
        $spammer = $this->createBadSpammer();

        static::assertTrue($spammer->isSameHost('http://bad-spammer.xyz'));
        static::assertFalse($spammer->isSameHost('http://google.com'));
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    private function createBadSpammer()
    {
        return new Spammer($url = 'http://bad-spammer.xyz', true);
    }
}
