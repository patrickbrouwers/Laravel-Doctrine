<?php

namespace Brouwers\LaravelDoctrine\Configuration\Cache;

use Doctrine\Common\Cache\PhpFileCache;

class FileCacheProvider extends AbstractCacheProvider
{
    /**
     * @var string
     */
    protected $name = 'file';

    /**
     * @param array $config
     *
     * @throws DriverNotFound
     * @return array
     */
    public function configure($config = [])
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return PhpFileCache
     */
    public function resolve()
    {
        return new PhpFileCache($this->config['path']);
    }
}
