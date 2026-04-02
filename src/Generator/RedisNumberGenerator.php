<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Generator;

use Predis\ClientInterface;
use Siganushka\OrderBundle\Entity\Order;

class RedisNumberGenerator implements OrderNumberGeneratorInterface
{
    private readonly \Redis|ClientInterface $redis;

    public function __construct(
        \Redis|ClientInterface|null $redis = null,
        private readonly int $seqStepMin = 10,
        private readonly int $seqStepMax = 50,
    ) {
        $this->redis = $redis ?? new \Redis();
    }

    public function generate(Order $order): string
    {
        $script = <<<'EOLUA'
                local current = redis.call('GET', KEYS[1])
                if not current then
                    local time = redis.call('TIME')
                    redis.call('SET', KEYS[1], 102400 + (tonumber(time[2]) % 90000))
                    redis.call('EXPIRE', KEYS[1], 172800)
                end
                return redis.call('INCRBY', KEYS[1], ARGV[1])
            EOLUA;

        $now = new \DateTime();
        $key = \sprintf('order:seq:%s', $prefix = \sprintf('%2s%03d', $now->format('y'), $now->format('z') + 1));

        $step = random_int($this->seqStepMin, $this->seqStepMax);
        /** @var int */
        $sequence = $this->redis->eval($script, [$key, $step], 1);

        return \sprintf('%5s%07d', $prefix, $sequence);
    }
}
