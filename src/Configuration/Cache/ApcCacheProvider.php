<?php

namespace Brouwers\LaravelDoctrine\Configuration\Cache;

use Brouwers\LaravelDoctrine\Exceptions\DriverNotFoundException;
use Doctrine\Common\Cache\ApcCache;

class ApcCacheProvider extends AbstractCacheProvider
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
        return $this;
    }

    /**
     * @throws DriverNotFoundException
     * @return ApcCache
     */
    public function resolve()
    {
        if (extension_loaded('apc')) {
            return new ApcCache();
        }

        throw new DriverNotFoundException('Apc extension not loaded');
    }
}
