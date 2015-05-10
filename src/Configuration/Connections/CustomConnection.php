<?php

namespace Brouwers\LaravelDoctrine\Configuration\Connections;

class CustomConnection extends AbstractConnection
{
    /**
     * @param array $config
     *
     * @return array
     */
    public function configure($config = [])
    {
        return new static($config);
    }
}
