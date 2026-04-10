<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Generator;

use Siganushka\OrderBundle\Entity\Order;

class RedisNumberGenerator implements OrderNumberGeneratorInterface
{
    private readonly \Redis|\RedisArray|\RedisCluster|\Predis\ClientInterface|\Relay\Relay|\Relay\Cluster $redis;

    public function __construct(\Redis|\RedisArray|\RedisCluster|\Predis\ClientInterface|\Relay\Relay|\Relay\Cluster|null $redis = null)
    {
        $this->redis = $redis ?? new \Redis();
    }

    public function generate(Order $order): string
    {
        $script = <<<'EOLUA'
                local step = tonumber(ARGV[1])
                local hour = tonumber(ARGV[2]) * 100000
                local exists = redis.call('EXISTS', KEYS[1])
                if exists == 0 then
                    local time = redis.call('TIME')
                    local data = hour + (tonumber(time[2]) % 61800)
                    redis.call('SET', KEYS[1], data, 'NX', 'EX', 90000)
                end
                return redis.call('INCRBY', KEYS[1], step)
            EOLUA;

        $now = new \DateTime();
        $key = \sprintf('order:sequence:%s', $prefix = \sprintf('%2s%03d', $now->format('y'), $now->format('z') + 1));

        $step = random_int(5, 15);
        /** @var int */
        $sequence = $this->redis->eval($script, [$key, $step, $now->format('G')], 1);

        return \sprintf('%s%07d', $prefix, $sequence);
    }
}
