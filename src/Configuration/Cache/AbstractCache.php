<?php

namespace Brouwers\LaravelDoctrine\Configuration\Cache;

use Doctrine\Common\Cache\Cache as DoctrineCache;

abstract class AbstractCache implements Cache
{
    /**
     * @var DoctrineCache
     */
    protected $cache;

    /**
     * @param DoctrineCache $cache
     * @param null          $name
     */
    public function __construct(DoctrineCache $cache = null, $name = null)
    {
        $this->cache = $cache;

        if ($name) {
            $this->name = $name;
        }
    }

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
