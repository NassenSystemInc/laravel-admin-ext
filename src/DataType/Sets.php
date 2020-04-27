<?php

namespace Encore\Admin\RedisManager\DataType;

class Sets extends DataType
{
    /**
     * {@inheritdoc}
     */
    public function fetch($key)
    {
        return $this->getClient()->smembers($key);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $params)
    {
        $key = $params['key'];

        if (!empty( $params['member'])) {
            $member = $params['member'];
            $this->getClient()->sadd($key, $member);
        }

        if (!empty( $params['_editable'])) {
            $new = $params['value'];
            $old = $params['pk'];

            $this->getClient()->transaction(function ($tx) use ($key, $old, $new) {
                $tx->srem($key, $old);
                $tx->sadd($key, $new);
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $params)
    {
        $key = $params['key'];
        $ttl = $params['ttl'];
        $members = $params['members'];

        $this->getClient()->sadd($key, $members);

        if ($ttl > 0) {
            $this->getClient()->expire($key, $ttl);
        }

//         return redirect(route('redis-edit-key', [
//             'conn' => request('conn'),
//             'key'  => $key,
//         ]));
    }

    /**
     * Remove a member from a set.
     *
     * @param array $params
     *
     * @return int
     */
    public function remove(array $params)
    {
        $key = $params['key'];
        $member = $params['member'];

        return $this->getClient()->srem($key, $member);
    }
}
