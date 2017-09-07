<?php namespace Arcanedev\SpamBlocker;

use Arcanedev\Support\PackageServiceProvider as ServiceProvider;

/**
 * Class     SpamBlockerServiceProvider
 *
 * @package  Arcanedev\SpamBlocker
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SpamBlockerServiceProvider extends ServiceProvider
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'spam-blocker';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register()
    {
        parent::register();

        $this->registerConfig();

        $this->registerSpamBlocker();
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        parent::boot();

        $this->publishConfig();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Contracts\SpamBlocker::class
        ];
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register the spam blocker
     */
    private function registerSpamBlocker()
    {
        $this->singleton(Contracts\SpamBlocker::class, function ($app) {
            /** @var  \Illuminate\Contracts\Config\Repository  $config */
            $config = $app['config'];

            return new SpamBlocker(
                $config->get('spam-blocker')
            );
        });
    }
}
