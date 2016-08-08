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

        $this->singleton('arcanedev.spam-blocker', function ($app) {
            /** @var \Illuminate\Contracts\Config\Repository $config */
            $config = $app['config'];

            return new SpamBlocker(
                $config->get('spam-blocker')
            );
        });

        $this->bind(Contracts\SpamBlocker::class, 'arcanedev.spam-blocker');
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        parent::boot();
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
}
