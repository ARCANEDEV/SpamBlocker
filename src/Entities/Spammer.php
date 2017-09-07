<?php namespace Arcanedev\SpamBlocker\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * Class     Spammer
 *
 * @package  Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Spammer implements Arrayable, Jsonable, JsonSerializable
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var string */
    protected $host;

    /** @var bool */
    protected $blocked;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Spammer constructor.
     *
     * @param  string  $host
     * @param  bool    $blocked
     */
    public function __construct($host, $blocked)
    {
        $this->setHost($host);
        $this->setBlocked($blocked);
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the host url.
     *
     * @return string
     */
    public function host()
    {
        return $this->host;
    }

    /**
     * Set the host url.
     *
     * @param  string  $host
     *
     * @return self
     */
    public function setHost($host)
    {
        $this->host = trim(utf8_encode($host));

        return $this;
    }

    /**
     * Get the blocked status.
     *
     * @return bool
     */
    public function isBlocked()
    {
        return $this->blocked;
    }

    /**
     * Set the blocked status.
     *
     * @param  bool  $blocked
     *
     * @return self
     */
    public function setBlocked($blocked)
    {
        $this->blocked = (bool) $blocked;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make a spammer instance.
     *
     * @param  string  $host
     * @param  bool    $blocked
     *
     * @return self
     */
    public static function make($host, $blocked = true)
    {
        return new static($host, $blocked);
    }

    /**
     * Check if the given host is the same as the spammer host.
     *
     * @param  string  $host
     *
     * @return bool
     */
    public function isSameHost($host)
    {
        return $this->host() === trim(utf8_encode($host));
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'host'    => $this->host,
            'blocked' => $this->blocked,
        ];
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}
