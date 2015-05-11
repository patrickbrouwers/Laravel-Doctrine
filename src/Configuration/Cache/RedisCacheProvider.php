<?php

namespace Brouwers\LaravelDoctrine\Configuration\Cache;

use Doctrine\Common\Cache\RedisCache;
use Redis;

class RedisCacheProvider extends AbstractCache
{
    /**
     * @var string
     */
    protected $name = 'redis';

    /**
     * @param array $config
     *
     * @throws DriverNotFoundException
     * @return array
     */
    public function configure($config = [])
    {
        $cache = new RedisCache();

        $redisConfig = config('database.redis.' . $config['connection']);

        if (extension_loaded('redis')) {
            $redis = new Redis();
            $redis->connect($redisConfig['host'], $redisConfig['port']);
            $redis->select($redisConfig['database']);

            $cache->setRedis($redis);
        }

        return new static(
            $cache
        );
    }
}
