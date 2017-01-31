<?php namespace Arcanedev\SpamBlocker\Tests;

use Arcanedev\SpamBlocker\SpamBlockerServiceProvider;

/**
 * Class     SpamBlockerServiceProviderTest
 *
 * @package  Arcanedev\SpamBlocker\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SpamBlockerServiceProviderTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var  \Arcanedev\SpamBlocker\SpamBlockerServiceProvider */
    private $provider;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $this->provider = $this->app->getProvider(SpamBlockerServiceProvider::class);
    }

    public function tearDown()
    {
        unset($this->provider);

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
            \Illuminate\Support\ServiceProvider::class,
            \Arcanedev\Support\ServiceProvider::class,
            \Arcanedev\Support\PackageServiceProvider::class,
            \Arcanedev\SpamBlocker\SpamBlockerServiceProvider::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->provider);
        }
    }

    /** @test */
    public function it_can_provides()
    {
        $expected = [
            \Arcanedev\SpamBlocker\Contracts\SpamBlocker::class,
        ];

        $this->assertSame($expected, $this->provider->provides());
    }
}
