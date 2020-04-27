<?php

namespace Encore\Admin\RedisManager\DataType;

class Hashes extends DataType
{
    /**
     * {@inheritdoc}
     */
    public function fetch($key)
    {
        return $this->getClient()->hgetall($key);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $params)
    {
        $key = $params['key'];

        if (!empty($params['field'])) {
            $field = $params['field'];
            $value = $params['value'];

            $this->getClient()->hset($key, $field, $value);
        }

        if (!empty($params['_editable'])) {
            $value = $params['value'];
            $field = $params['pk'];

            $this->getClient()->hset($key, $field, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $params)
    {
        $key = $params['key'];
        $ttl = $params['ttl'];
        $field = $params['field'];
        $value = $params['value'];

        $this->getClient()->hset($key, $field, $value);

        if ($ttl > 0) {
            $this->getClient()->expire($key, $ttl);
        }

//         return redirect(route('redis-edit-key', [
//             'conn' => request('conn'),
//             'key'  => $key,
//         ]));
    }

    /**
     * Remove a field from a hash.
     *
     * @param array $params
     *
     * @return int
     */
    public function remove(array $params)
    {
        $key = $params['key'];
        $field = $params['field'];

        return $this->getClient()->hdel($key, [$field]);
    }
}
