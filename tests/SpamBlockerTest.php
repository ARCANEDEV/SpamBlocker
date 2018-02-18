<?php namespace Arcanedev\SpamBlocker\Tests;

/**
 * Class     SpamBlockerTest
 *
 * @package  Arcanedev\SpamBlocker\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SpamBlockerTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\SpamBlocker\SpamBlocker */
    private $blocker;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp()
    {
        parent::setUp();

        $this->blocker = $this->app->make(\Arcanedev\SpamBlocker\Contracts\SpamBlocker::class);
    }

    public function tearDown()
    {
        unset($this->blocker);

        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated()
    {
        $expectations = [
            \Arcanedev\SpamBlocker\Contracts\SpamBlocker::class,
            \Arcanedev\SpamBlocker\SpamBlocker::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->blocker);
        }
    }

    /** @test */
    public function it_can_be_instantiated_via_contract()
    {
        $this->blocker = $this->app->make(\Arcanedev\SpamBlocker\Contracts\SpamBlocker::class);
        $expectations  = [
            \Arcanedev\SpamBlocker\Contracts\SpamBlocker::class,
            \Arcanedev\SpamBlocker\SpamBlocker::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->blocker);
        }
    }

    /** @test */
    public function it_can_get_spammers()
    {
        $spammers = $this->blocker->spammers();

        static::assertInstanceOf(
            \Arcanedev\SpamBlocker\Entities\SpammerCollection::class,
            $spammers
        );

        static::assertTrue($spammers->count() > 400);
    }

    /** @test */
    public function it_can_set_and_get_includes()
    {
        static::assertEmpty($this->blocker->getIncludes());

        $includes = [
            'evil-spammer.io',
            'cool-spammer.yeah',
        ];

        $this->blocker->setIncludes($includes);

        static::assertCount(count($includes), $this->blocker->getIncludes());
        static::assertSame($includes, $this->blocker->getIncludes());
    }

    /** @test */
    public function it_can_set_and_get_excludes()
    {
        static::assertEmpty($this->blocker->getExcludes());

        $excludes = [
            'google.com',
            'yahoo.com',
            'bing.com',
        ];

        $this->blocker->setExcludes($excludes);

        static::assertCount(count($excludes), $this->blocker->getExcludes());
        static::assertSame($excludes, $this->blocker->getExcludes());
    }

    /** @test */
    public function it_can_include_a_new_spammer_to_blacklist()
    {
        $spammers = $this->blocker->spammers();

        $this->blocker->block('new-spammer.com');

        static::assertNotEquals($spammers->count(), $this->blocker->all()->count());
    }

    /** @test */
    public function it_can_skip_adding_existing_spammer_in_blacklist()
    {
        $host     = '0n-line.tv';
        $spammers = $this->blocker->spammers();

        $this->blocker->block($host);

        static::assertCount($spammers->count(), $this->blocker->all());

        $spammer = $this->blocker->getSpammer($host);

        static::assertSame($host, $spammer->host());
        static::assertTrue($spammer->isBlocked());
    }

    /** @test */
    public function it_can_exclude_a_new_spammer_from_blacklist()
    {
        $host     = 'new-spammer.com';
        $spammers = $this->blocker->spammers();

        $this->blocker->allow($host);

        static::assertNotEquals($spammers->count(), $this->blocker->all()->count());

        $spammer = $this->blocker->getSpammer($host);

        static::assertSame($host, $spammer->host());
        static::assertFalse($spammer->isBlocked());
    }

    /** @test */
    public function it_can_set_and_get_source()
    {
        $path = 'source/to/spammer.txt';

        $this->blocker->setSource($path);

        static::assertSame($path, $this->blocker->getSource());
    }

    /**
     * @test
     *
     * @expectedException         \Arcanedev\SpamBlocker\Exceptions\SpammerSourceNotFound
     * @expectedExceptionMessage  The spammers source file not found in [source/to/spammer.txt].
     */
    public function it_must_throw_an_exception_when_source_file_not_found()
    {
        $this->blocker
             ->setSource('source/to/spammer.txt')
             ->load();
    }

    /** @test */
    public function it_can_check_is_blocked()
    {
        $host = 'http://0n-line.tv';

        static::assertTrue($this->blocker->isBlocked($host));
        static::assertFalse($this->blocker->isAllowed($host));
    }

    /** @test */
    public function it_can_check_is_allowed()
    {
        $host = 'http://google.com';

        static::assertTrue($this->blocker->isAllowed($host));
        static::assertFalse($this->blocker->isBlocked($host));
    }

    /** @test */
    public function it_can_reset()
    {
        $count = $this->blocker->all()->count();

        $this->blocker->block('http://0n-line.tv');
        $this->blocker->allow('http://google.com');

        static::assertCount(1, $this->blocker->getExcludes());
        static::assertCount(1, $this->blocker->getIncludes());
        static::assertCount($count + 2, $this->blocker->all());

        $this->blocker->reset();

        static::assertEmpty($this->blocker->getExcludes());
        static::assertEmpty($this->blocker->getIncludes());
        static::assertCount($count, $this->blocker->all());
    }
}
