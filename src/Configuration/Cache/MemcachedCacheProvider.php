<?php

namespace Brouwers\LaravelDoctrine\Configuration\Cache;

use Doctrine\Common\Cache\MemcachedCache;
use Memcached;

class MemcachedCacheProvider extends AbstractCache
{
    /**
     * @var string
     */
    protected $name = 'memcached';

    /**
     * @param array $config
     *
     * @throws DriverNotFoundException
     * @return array
     */
    public function configure($config = [])
    {
        $cache = new MemcachedCache();

        if (extension_loaded('memcached')) {
            $memcached = new Memcached();

            foreach ($config['servers'] as $server) {
                $memcached->addServer(
                    $server['host'],
                    $server['port'],
                    $server['weight']
                );
            }

            $cache->setMemcached($memcached);
        }

        return new static(
            $cache
        );
    }
}
