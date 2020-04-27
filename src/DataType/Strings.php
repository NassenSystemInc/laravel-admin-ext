<?php

namespace Encore\Admin\RedisManager\DataType;

class Strings extends DataType
{
    /**
     * {@inheritdoc}
     */
    public function fetch($key)
    {
        return $this->getClient()->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $params)
    {
        $this->store($params);
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $params)
    {
        $key = $params['key'];
        $value = $params['value'];
        $ttl = $params['ttl'];

        $this->getClient()->set($key, $value);

        if ($ttl > 0) {
            $this->getClient()->expire($key, $ttl);
        }
    }
}
