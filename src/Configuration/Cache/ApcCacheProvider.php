<?php

namespace Brouwers\LaravelDoctrine\Configuration\Cache;

use Doctrine\Common\Cache\ApcCache;

class ApcCacheProvider extends AbstractCache
{
    /**
     * @var string
     */
    protected $name = 'apc';

    /**
     * @param array $config
     *
     * @throws DriverNotFoundException
     * @return array
     */
    public function configure($config = [])
    {
        return new static(
            new ApcCache()
        );
    }
}
