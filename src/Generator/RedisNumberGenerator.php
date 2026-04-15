<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Generator;

use Siganushka\OrderBundle\Entity\Order;

class RedisNumberGenerator implements OrderNumberGeneratorInterface
{
    public function __construct(
        private readonly \Redis|\RedisArray|\RedisCluster|\Predis\ClientInterface|\Relay\Relay|\Relay\Cluster $redis = new \Redis(),
        private readonly string $redisKey = 'order:number',
        private readonly int $stepMin = 1,
        private readonly int $stepMax = 20,
    ) {
    }

    public function generate(Order $order): string
    {
        $script = <<<'EOLUA'
                local step = tonumber(ARGV[1])
                local hour = tonumber(ARGV[2]) * 100000
                local current = redis.call('GET', KEYS[1])
                if not current or tonumber(current) < hour then
                    local time = redis.call('TIME')
                    local data = hour + (tonumber(time[1]) % 60) * 1000 + (tonumber(time[2]) % 1000) + 1024
                    redis.call('SET', KEYS[1], data, 'EX', 90000)
                end
                return redis.call('INCRBY', KEYS[1], step)
            EOLUA;

        $now = new \DateTime();

        $prefix = \sprintf('%s%03d', $now->format('y'), $now->format('z') + 1);
        $key = \sprintf('%s:%05s', $this->redisKey, $prefix);

        $step = random_int($this->stepMin, $this->stepMax);
        /** @var int */
        $sequence = $this->redis->eval($script, [$key, $step, $now->format('G')], 1);

        return \sprintf('%s%07d', $prefix, $sequence);
    }
}
