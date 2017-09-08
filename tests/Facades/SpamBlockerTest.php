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

        $this->assertInstanceOf(
            \Arcanedev\SpamBlocker\Entities\SpammerCollection::class,
            $spammers
        );

        $this->assertTrue($spammers->count() > 400);
    }

    /** @test */
    public function it_can_set_and_get_includes()
    {
        $this->assertEmpty(SpamBlocker::getIncludes());

        $includes = [
            'evil-spammer.io',
            'cool-spammer.yeah',
        ];

        SpamBlocker::setIncludes($includes);

        $this->assertCount(count($includes), SpamBlocker::getIncludes());
        $this->assertSame($includes, SpamBlocker::getIncludes());
    }

    /** @test */
    public function it_can_set_and_get_excludes()
    {
        $this->assertEmpty(SpamBlocker::getExcludes());

        $excludes = [
            'google.com',
            'yahoo.com',
            'bing.com',
        ];

        SpamBlocker::setExcludes($excludes);

        $this->assertCount(count($excludes), SpamBlocker::getExcludes());
        $this->assertSame($excludes, SpamBlocker::getExcludes());
    }

    /** @test */
    public function it_can_include_a_new_spammer_to_blacklist()
    {
        $spammers = SpamBlocker::spammers();

        SpamBlocker::block('new-spammer.com');

        $this->assertNotEquals($spammers->count(), SpamBlocker::all()->count());
    }

    /** @test */
    public function it_can_skip_adding_existing_spammer_in_blacklist()
    {
        $host     = '0n-line.tv';
        $spammers = SpamBlocker::spammers();

        SpamBlocker::block($host);

        $this->assertCount($spammers->count(), SpamBlocker::all());

        $spammer = SpamBlocker::getSpammer($host);

        $this->assertSame($host, $spammer->host());
        $this->assertTrue($spammer->isBlocked());
    }

    /** @test */
    public function it_can_exclude_a_new_spammer_from_blacklist()
    {
        $host     = 'new-spammer.com';
        $spammers = SpamBlocker::spammers();

        SpamBlocker::allow($host);

        $this->assertNotEquals($spammers->count(), SpamBlocker::all()->count());

        $spammer = SpamBlocker::getSpammer($host);

        $this->assertSame($host, $spammer->host());
        $this->assertFalse($spammer->isBlocked());
    }

    /** @test */
    public function it_can_set_and_get_source()
    {
        $path = 'source/to/spammer.txt';

        SpamBlocker::setSource($path);

        $this->assertSame($path, SpamBlocker::getSource());
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

        $this->assertTrue(SpamBlocker::isBlocked($host));
        $this->assertFalse(SpamBlocker::isAllowed($host));
    }

    /** @test */
    public function it_can_check_is_allowed()
    {
        $host = 'http://google.com';

        $this->assertTrue(SpamBlocker::isAllowed($host));
        $this->assertFalse(SpamBlocker::isBlocked($host));
    }

    /** @test */
    public function it_can_reset()
    {
        $count = SpamBlocker::all()->count();

        SpamBlocker::block('http://0n-line.tv');
        SpamBlocker::allow('http://google.com');

        $this->assertCount(1, SpamBlocker::getExcludes());
        $this->assertCount(1, SpamBlocker::getIncludes());
        $this->assertCount($count + 2, SpamBlocker::all());

        SpamBlocker::reset();

        $this->assertEmpty(SpamBlocker::getExcludes());
        $this->assertEmpty(SpamBlocker::getIncludes());
        $this->assertCount($count, SpamBlocker::all());
    }
}
