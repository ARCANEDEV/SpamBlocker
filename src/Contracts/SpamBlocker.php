<?php namespace Arcanedev\SpamBlocker\Contracts;

/**
 * Interface  SpamBlocker
 *
 * @package   Arcanedev\LaravelReferralSpamBlocker\Contracts
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface SpamBlocker
{
    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the loaded spammers.
     *
     * @return \Arcanedev\SpamBlocker\Entities\Spammers
     */
    public function spammers();

    /**
     * Set the source path.
     *
     * @param  string  $source
     *
     * @return self
     */
    public function setSource($source);

    /**
     * Get the included spammers.
     *
     * @return array
     */
    public function getIncludes();

    /**
     * Set the included spammers.
     *
     * @param  array  $includes
     *
     * @return self
     */
    public function setIncludes(array $includes);

    /**
     * Get the excluded spammers.
     *
     * @return array
     */
    public function getExcludes();

    /**
     * Set the excluded spammers.
     *
     * @param  array  $excludes
     *
     * @return self
     */
    public function setExcludes(array $excludes);

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Load spammers.
     *
     * @return self
     */
    public function load();

    /**
     * Allow a host.
     *
     * @param  string  $host
     *
     * @return self
     */
    public function allow($host);

    /**
     * Block a host.
     *
     * @param  string  $host
     *
     * @return self
     */
    public function block($host);

    /**
     * Get all spammers (allowed and blocked one).
     *
     * @return \Arcanedev\SpamBlocker\Entities\Spammers
     */
    public function all();

    /**
     * Check if the given host is allowed.
     *
     * @param  string  $host
     *
     * @return bool
     */
    public function isAllowed($host);

    /**
     * Check if the given host is blocked.
     *
     * @param  string  $host
     *
     * @return bool
     */
    public function isBlocked($host);

    /**
     * Get a spammer.
     *
     * @param  string  $host
     *
     * @return \Arcanedev\SpamBlocker\Entities\Spammer|null
     */
    public function getSpammer($host);

    /**
     * Reset the spammers.
     *
     * @return self
     */
    public function reset();
}
