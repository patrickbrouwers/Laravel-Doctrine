<?php

namespace Brouwers\LaravelDoctrine\Configuration\Cache;

abstract class AbstractCacheProvider implements CacheProvider
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @return mixed
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @return string
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
}
