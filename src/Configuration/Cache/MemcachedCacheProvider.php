<?php

namespace Brouwers\LaravelDoctrine\Configuration\Cache;

use Brouwers\LaravelDoctrine\Exceptions\DriverNotFoundException;
use Doctrine\Common\Cache\MemcachedCache;
use Memcached;

class MemcachedCacheProvider extends AbstractCacheProvider
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
        $this->config = $config;

        return $this;
    }

    /**
     * @throws DriverNotFoundException
     * @return MemcachedCache
     */
    public function resolve()
    {
        $cache = new MemcachedCache();

        if (extension_loaded('memcached')) {
            $memcached = new Memcached();

            foreach ($this->config['servers'] as $server) {
                $memcached->addServer(
                    $server['host'],
                    $server['port'],
                    $server['weight']
                );
            }

            $cache->setMemcached($memcached);

            return $cache;
        }

        throw new DriverNotFoundException('Memcached extension not loaded');
    }
}
