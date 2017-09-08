<?php namespace Arcanedev\SpamBlocker\Entities;

use Illuminate\Support\Collection;

/**
 * Class     SpammerCollection
 *
 * @package  Arcanedev\SpamBlocker\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SpammerCollection extends Collection
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Load the items.
     *
     * @param  array  $spammers
     *
     * @return self
     */
    public static function load(array $spammers)
    {
        return (new self)->includes($spammers);
    }

    /**
     * Add many hosts to the blacklist collection.
     *
     * @param  array  $includes
     *
     * @return self
     */
    public function includes(array $includes)
    {
        foreach ($includes as $include) {
            $this->blockOne($include);
        }

        return $this;
    }

    /**
     * Add many hosts to the whitelist collection.
     *
     * @param  array  $excludes
     *
     * @return self
     */
    public function excludes(array $excludes)
    {
        foreach ($excludes as $exclude) {
            $this->allowOne($exclude);
        }

        return $this;
    }

    /**
     * Add a host to a blacklist collection.
     *
     * @param  string  $host
     *
     * @return self
     */
    public function blockOne($host)
    {
        return $this->exists($host)
            ? $this->updateOne($host)
            : $this->addOne($host);
    }

    /**
     * Add a host to a whitelist collection.
     *
     * @param  string  $host
     *
     * @return self
     */
    public function allowOne($host)
    {
        return $this->exists($host)
            ? $this->updateOne($host, false)
            : $this->addOne($host, false);
    }

    /**
     * Add a host to the collection.
     *
     * @param  string  $host
     * @param  bool    $blocked
     *
     * @return self
     */
    private function addOne($host, $blocked = true)
    {
        $spammer = Spammer::make($host, $blocked);

        $this->put($spammer->host(), $spammer);

        return $this;
    }

    /**
     * Update a host/spammer.
     *
     * @param  string  $host
     * @param  bool    $blocked
     *
     * @return self
     */
    private function updateOne($host, $blocked = true)
    {
        if ($this->exists($host)) {
            /** @var  \Arcanedev\SpamBlocker\Entities\Spammer  $spammer */
            $spammer = $this->get($host);

            if ($spammer->isBlocked() !== $blocked)
                $this->put($spammer->host(), $spammer->setBlocked($blocked));
        }

        return $this;
    }

    /**
     * Get a spammer from the collection.
     *
     * @param  string  $host
     *
     * @return \Arcanedev\SpamBlocker\Entities\Spammer|null
     */
    public function getOne($host)
    {
        return $this->get(
            $this->sanitizeHost($host)
        );
    }

    /**
     * Filter the spammer by the given hosts.
     *
     * @param  array  $hosts
     * @param  bool   $strict
     *
     * @return self
     */
    public function whereHostIn(array $hosts, $strict = false)
    {
        $values = $this->getArrayableItems($hosts);

        return $this->filter(function (Spammer $spammer) use ($values, $strict) {
            return in_array($spammer->host(), $values, $strict);
        });
    }

    /**
     * Get only the blocked spammers.
     *
     * @return self
     */
    public function whereBlocked()
    {
        return $this->filter(function (Spammer $spammer) {
            return $spammer->isBlocked();
        });
    }

    /**
     * Check if a spammer exists in the collection.
     *
     * @param  string  $host
     *
     * @return bool
     */
    public function exists($host)
    {
        return $this->has(
            $this->sanitizeHost($host)
        );
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Sanitize the host url.
     *
     * @param  string  $host
     *
     * @return string
     */
    private function sanitizeHost($host)
    {
        return trim(utf8_encode($host));
    }
}
