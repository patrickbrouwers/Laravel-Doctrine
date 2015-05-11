<?php

namespace Brouwers\LaravelDoctrine\Configuration\Cache;

class CustomCacheProvider extends AbstractCache
{
    /**
     * @param array $config
     *
     * @return array
     */
    public function configure($config = [])
    {
        return new static();
    }
}
