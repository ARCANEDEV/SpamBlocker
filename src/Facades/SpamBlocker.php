<?php namespace Arcanedev\SpamBlocker\Facades;

use Arcanedev\SpamBlocker\Contracts\SpamBlocker as SpamBlockerContract;
use Illuminate\Support\Facades\Facade;

/**
 * Class     SpamBlocker
 *
 * @package  Arcanedev\SpamBlocker\Facades
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SpamBlocker extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return SpamBlockerContract::class; }
}
