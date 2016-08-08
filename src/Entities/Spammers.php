<?php namespace Arcanedev\SpamBlocker\Entities;

use Arcanedev\Support\Collection;

/**
 * Class     Spammers
 *
 * @package  Arcanedev\SpamBlocker\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Spammers extends Collection
{
    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Load the items.
     *
     * @param  array  $spammers
     *
     * @return static
     */
    public static function load(array $spammers)
    {
        return static::make()->includes($spammers);
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
     * @return static
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
     * @return static
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
     * @return static
     */
    private function addOne($host, $blocked = true)
    {
        $this->push([
            'host'  => trim(utf8_encode($host)),
            'block' => $blocked,
        ]);

        return $this;
    }

    /**
     * Update a host/spammer.
     *
     * @param  string  $host
     * @param  bool    $blocked
     *
     * @return static
     */
    private function updateOne($host, $blocked = true)
    {
        $this->items = $this->map(function ($spammer) use ($host, $blocked) {
            if ($spammer['host'] === $host && $spammer['block'] !== $blocked) {
                $spammer['block'] = $blocked;
            }

            return $spammer;
        })->all();

        return $this;
    }

    /**
     * Get a spammer from the collection.
     *
     * @param  string  $host
     *
     * @return array|null
     */
    public function getOne($host)
    {
        return $this->where('host', trim(utf8_encode($host)))
            ->first();
    }

    /**
     * Filter the spammer by the given hosts.
     *
     * @param  array  $hosts
     * @param  bool   $strict
     *
     * @return static
     */
    public function whereHostIn(array $hosts, $strict = false)
    {
        $values = $this->getArrayableItems($hosts);

        return $this->filter(function ($item) use ($values, $strict) {
            return in_array(data_get($item, 'host'), $values, $strict);
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
        return $this->getOne($host) !== null;
    }
}
