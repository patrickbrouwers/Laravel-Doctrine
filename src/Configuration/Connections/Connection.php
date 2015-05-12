<?php

namespace Brouwers\LaravelDoctrine\Configuration\Connections;

use Brouwers\LaravelDoctrine\Configuration\Driver;

interface Connection extends Driver
{
    /**
     * @param array $config
     *
     * @return array
     */
    public function configure($config = []);
}
