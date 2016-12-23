<?php namespace Arcanedev\SpamBlocker\Tests;

/**
 * Class     SpamBlockerTest
 *
 * @package  Arcanedev\SpamBlocker\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SpamBlockerTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var  \Arcanedev\SpamBlocker\SpamBlocker */
    private $blocker;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $this->blocker = $this->app->make('arcanedev.spam-blocker');
    }

    public function tearDown()
    {
        unset($this->blocker);

        parent::tearDown();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    /** @test */
    public function it_can_be_instantiated()
    {
        $expectations = [
            \Arcanedev\SpamBlocker\Contracts\SpamBlocker::class,
            \Arcanedev\SpamBlocker\SpamBlocker::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->blocker);
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
            $this->assertInstanceOf($expected, $this->blocker);
        }
    }

    /** @test */
    public function it_can_get_spammers()
    {
        $spammers = $this->blocker->spammers();

        $this->assertInstanceOf(
            \Arcanedev\SpamBlocker\Entities\Spammers::class,
            $spammers
        );

        $this->assertTrue($spammers->count() > 400);
    }

    /** @test */
    public function it_can_set_and_get_includes()
    {
        $this->assertEmpty($this->blocker->getIncludes());

        $includes = [
            'evil-spammer.io',
            'cool-spammer.yeah',
        ];

        $this->blocker->setIncludes($includes);

        $this->assertCount(count($includes), $this->blocker->getIncludes());
        $this->assertSame($includes, $this->blocker->getIncludes());
    }

    /** @test */
    public function it_can_set_and_get_excludes()
    {
        $this->assertEmpty($this->blocker->getExcludes());

        $excludes = [
            'google.com',
            'yahoo.com',
            'bing.com',
        ];

        $this->blocker->setExcludes($excludes);

        $this->assertCount(count($excludes), $this->blocker->getExcludes());
        $this->assertSame($excludes, $this->blocker->getExcludes());
    }

    /** @test */
    public function it_can_include_a_new_spammer_to_blacklist()
    {
        $spammers = $this->blocker->spammers();

        $this->blocker->block('new-spammer.com');

        $this->assertNotEquals($spammers->count(), $this->blocker->all()->count());
    }

    /** @test */
    public function it_can_skip_adding_existing_spammer_in_blacklist()
    {
        $host     = '0n-line.tv';
        $spammers = $this->blocker->spammers();

        $this->blocker->block($host);

        $this->assertCount($spammers->count(), $this->blocker->all());

        $spammer = $this->blocker->getSpammer($host);

        $this->assertSame($host, $spammer->host());
        $this->assertTrue($spammer->isBlocked());
    }

    /** @test */
    public function it_can_exclude_a_new_spammer_from_blacklist()
    {
        $host     = 'new-spammer.com';
        $spammers = $this->blocker->spammers();

        $this->blocker->allow($host);

        $this->assertNotEquals($spammers->count(), $this->blocker->all()->count());

        $spammer = $this->blocker->getSpammer($host);

        $this->assertSame($host, $spammer->host());
        $this->assertFalse($spammer->isBlocked());
    }

    /** @test */
    public function it_can_set_and_get_source()
    {
        $path = 'source/to/spammer.txt';

        $this->blocker->setSource($path);

        $this->assertSame($path, $this->blocker->getSource());
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

        $this->assertTrue($this->blocker->isBlocked($host));
        $this->assertFalse($this->blocker->isAllowed($host));
    }

    /** @test */
    public function it_can_check_is_allowed()
    {
        $host = 'http://google.com';

        $this->assertTrue($this->blocker->isAllowed($host));
        $this->assertFalse($this->blocker->isBlocked($host));
    }

    /** @test */
    public function it_can_reset()
    {
        $count = $this->blocker->all()->count();

        $this->blocker->block('http://0n-line.tv');
        $this->blocker->allow('http://google.com');

        $this->assertCount(1, $this->blocker->getExcludes());
        $this->assertCount(1, $this->blocker->getIncludes());
        $this->assertCount($count + 2, $this->blocker->all());

        $this->blocker->reset();

        $this->assertEmpty($this->blocker->getExcludes());
        $this->assertEmpty($this->blocker->getIncludes());
        $this->assertCount($count, $this->blocker->all());
    }
}
