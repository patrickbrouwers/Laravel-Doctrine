<?php

namespace Brouwers\LaravelDoctrine\Configuration\Cache;

use Brouwers\LaravelDoctrine\Configuration\Driver;

interface Cache extends Driver
{
    /**
     * @param array $config
     *
     * @return array
     */
    public function configure($config = []);
}
