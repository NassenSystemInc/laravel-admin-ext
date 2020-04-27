<?php

namespace Encore\Admin\RedisManager\DataType;

class Lists extends DataType
{
    /**
     * {@inheritdoc}
     */
    public function fetch($key)
    {
        return $this->getClient()->lrange($key, 0, -1);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $params)
    {
        $key = $params['key'];

        if (!empty($params['push'])) {
            $item = $params['item'];
            $command = $params['push'] == 'left' ? 'lpush' : 'rpush';

            $this->getClient()->{$command}($key, $item);
        }

        if (!empty($params['_editable'])) {
            $value = $params['value'];
            $index = $params['pk'];

            $this->getClient()->lset($key, $index, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $params)
    {
        $key = $params['key'];
        $item = $params['item'];
        $ttl = $params['ttl'];

        $this->getClient()->rpush($key, [$item]);

        if ($ttl > 0) {
            $this->getClient()->expire($key, $ttl);
        }

//         return redirect(route('redis-edit-key', [
//             'conn' => request('conn'),
//             'key'  => $key,
//         ]));
    }

    /**
     * Remove a member from list by index.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function remove(array $params)
    {
        $key = $params['key'];
        $index = $params['index'];

        $lua = <<<'LUA'
redis.call('lset', KEYS[1], ARGV[1], '__DELETED__');
redis.call('lrem', KEYS[1], 1, '__DELETED__');
LUA;

        return $this->getClient()->eval($lua, 1, $key, $index);
    }
}
