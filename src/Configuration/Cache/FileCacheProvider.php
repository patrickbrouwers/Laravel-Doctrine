<?php

namespace Brouwers\LaravelDoctrine\Configuration\Cache;

use Doctrine\Common\Cache\PhpFileCache;

class FileCacheProvider extends AbstractCache
{
    /**
     * @var string
     */
    protected $name = 'file';

    /**
     * @param array $config
     *
     * @throws DriverNotFoundException
     * @return array
     */
    public function configure($config = [])
    {
        return new static(
            new PhpFileCache($config['path'])
        );
    }
}
