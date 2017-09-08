<?php namespace Arcanedev\SpamBlocker;

use Arcanedev\SpamBlocker\Contracts\SpamBlocker as SpamBlockerContract;
use Arcanedev\SpamBlocker\Entities\SpammerCollection;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

/**
 * Class     SpamBlocker
 *
 * @package  Arcanedev\SpamBlocker
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SpamBlocker implements SpamBlockerContract
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The spammers source path.
     *
     * @var string|null
     */
    protected $source;

    /**
     * The included spammers.
     *
     * @var array
     */
    protected $includes = [];

    /**
     * The excluded spammers.
     *
     * @var array
     */
    protected $excludes = [];

    /**
     * The spammers collection.
     *
     * @var \Arcanedev\SpamBlocker\Entities\SpammerCollection
     */
    protected $spammers;

    /**
     * Cache key.
     *
     * @var  string
     */
    protected $cacheKey;

    /**
     * Cache expiration duration.
     *
     * @var int
     */
    protected $cacheExpires;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * SpamBlocker constructor.
     *
     * @param  array  $config
     */
    public function __construct(array $config)
    {
        $this->setSource(Arr::get($config, 'source', null));
        $this->setIncludes(Arr::get($config, 'include', []));
        $this->setExcludes(Arr::get($config, 'exclude', []));
        $this->cacheKey     = Arr::get($config, 'cache.key', 'arcanedev.spammers');
        $this->cacheExpires = Arr::get($config, 'cache.expires', 24 * 60);

        $this->load();
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the loaded spammers.
     *
     * @return \Arcanedev\SpamBlocker\Entities\SpammerCollection
     */
    public function spammers()
    {
        return $this->spammers;
    }

    /**
     * Get the spammer source file.
     *
     * @return string|null
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the source path.
     *
     * @param  string  $source
     *
     * @return self
     */
    public function setSource($source)
    {
        if ( ! empty($source)) {
            $this->source = $source;
        }

        return $this;
    }

    /**
     * Get the included spammers.
     *
     * @return array
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    /**
     * Set the included spammers.
     *
     * @param  array  $includes
     *
     * @return self
     */
    public function setIncludes(array $includes)
    {
        $this->includes = $includes;

        return $this;
    }

    /**
     * Get the excluded spammers.
     *
     * @return array
     */
    public function getExcludes()
    {
        return $this->excludes;
    }

    /**
     * Set the excluded spammers.
     *
     * @param  array  $excludes
     *
     * @return self
     */
    public function setExcludes(array $excludes)
    {
        $this->excludes = $excludes;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Load spammers.
     *
     * @return self
     */
    public function load()
    {
        $this->checkSource();

        $this->spammers = $this->cacheSpammers(function () {
            return SpammerCollection::load(
                file($this->source, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
            );
        });

        return $this;
    }

    /**
     * Allow a host.
     *
     * @param  string  $host
     *
     * @return self
     */
    public function allow($host)
    {
        if ( ! empty($host)) {
            $this->excludes[] = $host;
        }

        return $this;
    }

    /**
     * Block a host.
     *
     * @param  string  $host
     *
     * @return self
     */
    public function block($host)
    {
        if ( ! empty($host)) {
            $this->includes[] = $host;
        }

        return $this;
    }

    /**
     * Get all spammers (allowed and blocked ones).
     *
     * @return \Arcanedev\SpamBlocker\Entities\SpammerCollection
     */
    public function all()
    {
        return $this->spammers()
            ->includes($this->includes)
            ->excludes($this->excludes);
    }

    /**
     * Check if the given host is allowed.
     *
     * @param  string  $host
     *
     * @return bool
     */
    public function isAllowed($host)
    {
        return ! $this->isBlocked($host);
    }

    /**
     * Check if the given host is blocked.
     *
     * @param  string  $host
     *
     * @return bool
     */
    public function isBlocked($host)
    {
        $host = parse_url($host, PHP_URL_HOST);
        $host = utf8_encode(trim($host));

        if (empty($host)) return false;

        $fullDomain = $this->getFullDomain($host);
        $rootDomain = $this->getRootDomain($fullDomain);

        return $this->spammers()
            ->whereHostIn([$fullDomain, $rootDomain])
            ->whereBlocked()
            ->count() > 0;
    }

    /**
     * Get a spammer.
     *
     * @param  string  $host
     *
     * @return \Arcanedev\SpamBlocker\Entities\Spammer|null
     */
    public function getSpammer($host)
    {
        return $this->all()->getOne($host);
    }

    /**
     * Reset the spammers.
     *
     * @return self
     */
    public function reset()
    {
        return $this->resetCache()
             ->setIncludes([])
             ->setExcludes([])
             ->load();
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the full domain.
     *
     * @param  string  $host
     *
     * @return string
     */
    protected function getFullDomain($host)
    {
        preg_match('/^(?:[^@\n]+@)?(?:www\.)?([^:\/\n]+)/', $host, $matches);

        return $matches[1];
    }

    /**
     * Get the root domain.
     *
     * @param  string  $domain
     *
     * @return string
     */
    private function getRootDomain($domain)
    {
        $domainParts = explode('.', $domain);
        $count       = count($domainParts);

        return $count > 1
            ? $domainParts[$count - 2].'.'.$domainParts[$count - 1]
            : $domainParts[0];
    }

    /**
     * Reset the cache.
     *
     * @return self
     */
    protected function resetCache()
    {
        Cache::forget($this->cacheKey);

        return $this;
    }

    /**
     * Cache the spammers.
     *
     * @param  \Closure  $callback
     *
     * @return \Arcanedev\SpamBlocker\Entities\SpammerCollection
     */
    private function cacheSpammers(Closure $callback)
    {
        return Cache::remember($this->cacheKey, $this->cacheExpires, $callback);
    }

    /**
     * Check the source file.
     */
    private function checkSource()
    {
        if ( ! file_exists($this->source)) {
            throw new Exceptions\SpammerSourceNotFound(
                "The spammers source file not found in [{$this->source}]."
            );
        }
    }
}
