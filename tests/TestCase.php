<?php namespace Arcanedev\SpamBlocker\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Class     TestCase
 *
 * @package  Arcanedev\SpamBlocker\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class TestCase extends BaseTestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Arcanedev\SpamBlocker\SpamBlockerServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'SpamBlocker' => \Arcanedev\SpamBlocker\Facades\SpamBlocker::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     */
    protected function getEnvironmentSetUp($app)
    {
        /** @var  \Illuminate\Contracts\Config\Repository  $config */
        $config = $app['config'];

        // Setup default database to use sqlite :memory:
        $config->set(
            'spam-blocker.source',
            __DIR__ . '/../vendor/matomo/referrer-spam-blacklist/spammers.txt'
        );

        $this->registerRoutes($app);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register routes.
     *
     * @param  \Illuminate\Foundation\Application   $app
     */
    private function registerRoutes($app)
    {
        $app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->pushMiddleware(\Arcanedev\SpamBlocker\Http\Middleware\BlockReferralSpam::class);

        /** @var \Illuminate\Contracts\Routing\Registrar $router */
        $router = $app['router'];

        $router->get('/', function () {
            return 'Hello World!';
        });
    }
}
