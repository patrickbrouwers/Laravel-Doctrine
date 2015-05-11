<?php

namespace Brouwers\LaravelDoctrine\Configuration\Cache;

use Doctrine\Common\Cache\ArrayCache;

class ArrayCacheProvider extends AbstractCache
{
    /**
     * @var string
     */
    protected $name = 'array';

    /**
     * @param array $config
     *
     * @throws DriverNotFoundException
     * @return array
     */
    public function configure($config = [])
    {
        return new static(
            new ArrayCache()
        );
    }
}
