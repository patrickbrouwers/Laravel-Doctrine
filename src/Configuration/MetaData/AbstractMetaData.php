<?php

namespace Brouwers\LaravelDoctrine\Configuration\MetaData;

use Brouwers\LaravelDoctrine\Configuration\Cache\CacheManager;
use Doctrine\ORM\Configuration;

abstract class AbstractMetaData implements MetaData
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var
     */
    protected $name;

    /**
     * @param Configuration $config
     * @param null          $name
     */
    public function __construct(Configuration $config = null, $name = null)
    {
        $this->config = $config;

        if ($name) {
            $this->name = $name;
        }
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCache()
    {
        if (config('cache.default')) {
            return CacheManager::resolve(
                config('cache.default')
            )->getCache();
        }
    }
}
