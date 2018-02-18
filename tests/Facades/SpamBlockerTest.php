<?php namespace Arcanedev\SpamBlocker\Tests\Facades;

use Arcanedev\SpamBlocker\Tests\TestCase;
use Arcanedev\SpamBlocker\Facades\SpamBlocker;

/**
 * Class     SpamBlockerTest
 *
 * @package  Arcanedev\SpamBlocker\Tests\Facades
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SpamBlockerTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_get_spammers()
    {
        $spammers = SpamBlocker::spammers();

        static::assertInstanceOf(
            \Arcanedev\SpamBlocker\Entities\SpammerCollection::class,
            $spammers
        );

        static::assertTrue($spammers->count() > 400);
    }

    /** @test */
    public function it_can_set_and_get_includes()
    {
        static::assertEmpty(SpamBlocker::getIncludes());

        $includes = [
            'evil-spammer.io',
            'cool-spammer.yeah',
        ];

        SpamBlocker::setIncludes($includes);

        static::assertCount(count($includes), SpamBlocker::getIncludes());
        static::assertSame($includes, SpamBlocker::getIncludes());
    }

    /** @test */
    public function it_can_set_and_get_excludes()
    {
        static::assertEmpty(SpamBlocker::getExcludes());

        $excludes = [
            'google.com',
            'yahoo.com',
            'bing.com',
        ];

        SpamBlocker::setExcludes($excludes);

        static::assertCount(count($excludes), SpamBlocker::getExcludes());
        static::assertSame($excludes, SpamBlocker::getExcludes());
    }

    /** @test */
    public function it_can_include_a_new_spammer_to_blacklist()
    {
        $spammers = SpamBlocker::spammers();

        SpamBlocker::block('new-spammer.com');

        static::assertNotEquals($spammers->count(), SpamBlocker::all()->count());
    }

    /** @test */
    public function it_can_skip_adding_existing_spammer_in_blacklist()
    {
        $host     = '0n-line.tv';
        $spammers = SpamBlocker::spammers();

        SpamBlocker::block($host);

        static::assertCount($spammers->count(), SpamBlocker::all());

        $spammer = SpamBlocker::getSpammer($host);

        static::assertSame($host, $spammer->host());
        static::assertTrue($spammer->isBlocked());
    }

    /** @test */
    public function it_can_exclude_a_new_spammer_from_blacklist()
    {
        $host     = 'new-spammer.com';
        $spammers = SpamBlocker::spammers();

        SpamBlocker::allow($host);

        static::assertNotEquals($spammers->count(), SpamBlocker::all()->count());

        $spammer = SpamBlocker::getSpammer($host);

        static::assertSame($host, $spammer->host());
        static::assertFalse($spammer->isBlocked());
    }

    /** @test */
    public function it_can_set_and_get_source()
    {
        $path = 'source/to/spammer.txt';

        SpamBlocker::setSource($path);

        static::assertSame($path, SpamBlocker::getSource());
    }

    /**
     * @test
     *
     * @expectedException         \Arcanedev\SpamBlocker\Exceptions\SpammerSourceNotFound
     * @expectedExceptionMessage  The spammers source file not found in [source/to/spammer.txt].
     */
    public function it_must_throw_an_exception_when_source_file_not_found()
    {
        SpamBlocker::setSource('source/to/spammer.txt')->load();
    }

    /** @test */
    public function it_can_check_is_blocked()
    {
        $host = 'http://0n-line.tv';

        static::assertTrue(SpamBlocker::isBlocked($host));
        static::assertFalse(SpamBlocker::isAllowed($host));
    }

    /** @test */
    public function it_can_check_is_allowed()
    {
        $host = 'http://google.com';

        static::assertTrue(SpamBlocker::isAllowed($host));
        static::assertFalse(SpamBlocker::isBlocked($host));
    }

    /** @test */
    public function it_can_reset()
    {
        $count = SpamBlocker::all()->count();

        SpamBlocker::block('http://0n-line.tv');
        SpamBlocker::allow('http://google.com');

        static::assertCount(1, SpamBlocker::getExcludes());
        static::assertCount(1, SpamBlocker::getIncludes());
        static::assertCount($count + 2, SpamBlocker::all());

        SpamBlocker::reset();

        static::assertEmpty(SpamBlocker::getExcludes());
        static::assertEmpty(SpamBlocker::getIncludes());
        static::assertCount($count, SpamBlocker::all());
    }
}
