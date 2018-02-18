<?php namespace Arcanedev\SpamBlocker\Tests\Entities;

use Arcanedev\SpamBlocker\Entities\SpammerCollection;
use Arcanedev\SpamBlocker\Tests\TestCase;

/**
 * Class     SpammerCollectionTest
 *
 * @package  Arcanedev\SpamBlocker\Tests\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SpammerCollectionTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\SpamBlocker\Entities\SpammerCollection */
    private $spammers;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp()
    {
        parent::setUp();

        $this->spammers = new SpammerCollection;
    }

    public function tearDown()
    {
        unset($this->spammers);

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
            \Illuminate\Support\Collection::class,
            \Arcanedev\SpamBlocker\Entities\SpammerCollection::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->spammers);
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

        $this->spammers = SpammerCollection::load($spammers);

        static::assertCount(count($spammers), $this->spammers);

        foreach ($this->spammers as $spammer) {
            static::assertTrue(in_array($spammer->host(), $spammers));
            static::assertTrue($spammer->isBlocked());
        }
    }

    /** @test */
    public function it_can_add_one_to_blacklist()
    {
        $host = 'cool-spams.io';

        $this->spammers->blockOne($host);

        static::assertTrue($this->spammers->exists($host));

        $spammer = $this->spammers->getOne($host);

        static::assertSame($host, $spammer->host());
        static::assertTrue($spammer->isBlocked());
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
            static::assertTrue($this->spammers->exists($host));

            $spammer = $this->spammers->getOne($host);

            static::assertSame($host, $spammer->host());
            static::assertTrue($spammer->isBlocked());
        }
    }

    /** @test */
    public function it_can_add_one_to_whitelist()
    {
        $host = 'useful-spams.io';

        $this->spammers->allowOne($host);

        static::assertTrue($this->spammers->exists($host));

        $spammer = $this->spammers->getOne($host);

        static::assertSame($host, $spammer->host());
        static::assertFalse($spammer->isBlocked());
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
            static::assertTrue($this->spammers->exists($host));

            $spammer = $this->spammers->getOne($host);

            static::assertSame($host, $spammer->host());
            static::assertFalse($spammer->isBlocked());
        }
    }

    /** @test */
    public function it_can_block_and_allow_a_host()
    {
        $host = 'example.com';

        $this->spammers->blockOne($host);
        $spammer = $this->spammers->getOne($host);

        static::assertSame($host, $spammer->host());
        static::assertTrue($spammer->isBlocked());

        $this->spammers->allowOne($host);
        $spammer = $this->spammers->getOne($host);

        static::assertSame($host, $spammer->host());
        static::assertFalse($spammer->isBlocked());
    }

    /** @test */
    public function it_can_allow_and_block_a_host()
    {
        $host = 'example.com';

        $this->spammers->allowOne($host);
        $spammer = $this->spammers->getOne($host);

        static::assertSame($host, $spammer->host());
        static::assertFalse($spammer->isBlocked());

        $this->spammers->blockOne($host);
        $spammer = $this->spammers->getOne($host);

        static::assertSame($host, $spammer->host());
        static::assertTrue($spammer->isBlocked());
    }
}
