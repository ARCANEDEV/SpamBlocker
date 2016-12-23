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
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'spam-blocker';

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the base path of the package.
     *
     * @return string
     */
    public function getBasePath()
    {
        return dirname(__DIR__);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Register the service provider.
     */
    public function register()
    {
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
            'arcanedev.spam-blocker',
            Contracts\SpamBlocker::class
        ];
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
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

        $this->singleton('arcanedev.spam-blocker', Contracts\SpamBlocker::class);
    }
}
