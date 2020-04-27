<?php

namespace Encore\Admin\RedisManager\DataType;

use Predis\Client;

abstract class DataType
{
    /**
     * @var $client
     */
    protected $client;

    /**
     * DataType constructor.
     *
     * @param $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Get redis Client.
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    abstract public function fetch($key);

    /**
     * @param array $params
     *
     * @return mixed
     */
    abstract public function update(array $params);

    /**
     * @param array $params
     *
     * @return mixed
     */
    abstract public function store(array $params);

    /**
     * Returns the remaining time to live of a key that has a timeout.
     *
     * @param string $key
     *
     * @return int
     */
    public function ttl($key)
    {
        return $this->getClient()->ttl($key);
    }

    /**
     * Set a timeout on key.
     *
     * @param string $key
     * @param int    $expire
     *
     * @return void
     */
    public function setTtl($key, $expire)
    {
        if (is_null($expire)) {
            return;
        }

        $expire = (int) $expire;

        if ($expire > 0) {
            $this->getClient()->expire($key, $expire);
        } else {
            $this->getClient()->persist($key);
        }
    }
}
