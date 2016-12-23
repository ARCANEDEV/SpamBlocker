<?php namespace Arcanedev\SpamBlocker\Tests\Entities;

use Arcanedev\SpamBlocker\Entities\Spammers;
use Arcanedev\SpamBlocker\Tests\TestCase;

/**
 * Class     SpammersTest
 *
 * @package  Arcanedev\SpamBlocker\Tests\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SpammersTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var  \Arcanedev\SpamBlocker\Entities\Spammers */
    private $spammers;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $this->spammers = new Spammers;
    }

    public function tearDown()
    {
        unset($this->spammers);

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
            \Illuminate\Support\Collection::class,
            \Arcanedev\Support\Collection::class,
            \Arcanedev\SpamBlocker\Entities\Spammers::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->spammers);
        }
    }

    /** @test */
    public function it_can_load_spammers()
    {
        $spammers = [
            'spammer-one.com',
            'spammer-two.com',
            'spammer-three.com',
        ];

        $this->spammers = Spammers::load($spammers);

        $this->assertCount(count($spammers), $this->spammers);

        foreach ($this->spammers as $spammer) {
            $this->assertTrue(in_array($spammer->host(), $spammers));
            $this->assertTrue($spammer->isBlocked());
        }
    }

    /** @test */
    public function it_can_add_one_to_blacklist()
    {
        $host = 'cool-spams.io';

        $this->spammers->blockOne($host);

        $this->assertTrue($this->spammers->exists($host));

        $spammer = $this->spammers->getOne($host);

        $this->assertSame($host, $spammer->host());
        $this->assertTrue($spammer->isBlocked());
    }

    /** @test */
    public function it_can_add_many_to_blacklist()
    {
        $spammers = [
            'spammer-one.com',
            'spammer-two.com',
            'spammer-three.com',
        ];

        $this->spammers->includes($spammers);

        foreach ($spammers as $host) {
            $this->assertTrue($this->spammers->exists($host));

            $spammer = $this->spammers->getOne($host);

            $this->assertSame($host, $spammer->host());
            $this->assertTrue($spammer->isBlocked());
        }
    }

    /** @test */
    public function it_can_add_one_to_whitelist()
    {
        $host = 'useful-spams.io';

        $this->spammers->allowOne($host);

        $this->assertTrue($this->spammers->exists($host));

        $spammer = $this->spammers->getOne($host);

        $this->assertSame($host, $spammer->host());
        $this->assertFalse($spammer->isBlocked());
    }

    /** @test */
    public function it_can_add_many_to_whitelist()
    {
        $spammers = [
            'spammer-one.com',
            'spammer-two.com',
            'spammer-three.com',
        ];

        $this->spammers->excludes($spammers);

        foreach ($spammers as $host) {
            $this->assertTrue($this->spammers->exists($host));

            $spammer = $this->spammers->getOne($host);

            $this->assertSame($host, $spammer->host());
            $this->assertFalse($spammer->isBlocked());
        }
    }

    /** @test */
    public function it_can_block_and_allow_a_host()
    {
        $host = 'example.com';

        $this->spammers->blockOne($host);
        $spammer = $this->spammers->getOne($host);

        $this->assertSame($host, $spammer->host());
        $this->assertTrue($spammer->isBlocked());

        $this->spammers->allowOne($host);
        $spammer = $this->spammers->getOne($host);

        $this->assertSame($host, $spammer->host());
        $this->assertFalse($spammer->isBlocked());
    }

    /** @test */
    public function it_can_allow_and_block_a_host()
    {
        $host = 'example.com';

        $this->spammers->allowOne($host);
        $spammer = $this->spammers->getOne($host);

        $this->assertSame($host, $spammer->host());
        $this->assertFalse($spammer->isBlocked());

        $this->spammers->blockOne($host);
        $spammer = $this->spammers->getOne($host);

        $this->assertSame($host, $spammer->host());
        $this->assertTrue($spammer->isBlocked());
    }
}
