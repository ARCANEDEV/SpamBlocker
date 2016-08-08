<?php namespace Arcanedev\SpamBlocker;

use Arcanedev\SpamBlocker\Contracts\SpamBlocker as SpamBlockerContract;
use Arcanedev\SpamBlocker\Entities\Spammers;
use Illuminate\Support\Arr;

/**
 * Class     SpamBlocker
 *
 * @package  Arcanedev\SpamBlocker
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SpamBlocker implements SpamBlockerContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
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
     * @var \Arcanedev\SpamBlocker\Entities\Spammers
     */
    protected $spammers;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
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

        $this->load();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the loaded spammers.
     *
     * @return \Arcanedev\SpamBlocker\Entities\Spammers
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

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Load spammers.
     *
     * @return self
     */
    public function load()
    {
        if ( ! file_exists($this->source)) {
            throw new Exceptions\SpammerSourceNotFound(
                "The spammers source file not found in [{$this->source}]."
            );
        }

        $this->spammers = Spammers::load(
            file($this->source, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
        );

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
     * Get all spammers (allowed and blocked one).
     *
     * @return \Arcanedev\SpamBlocker\Entities\Spammers
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
            ->where('block', true)
            ->whereIn('host', [$fullDomain, $rootDomain])
            ->count() > 0;
    }

    /**
     * Get a spammer.
     *
     * @param  string  $host
     *
     * @return array|null
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
        return $this->setIncludes([])
             ->setExcludes([])
             ->load();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
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
            ? $domainParts[$count - 2] . '.' . $domainParts[$count - 1]
            : $domainParts[0];
    }
}
