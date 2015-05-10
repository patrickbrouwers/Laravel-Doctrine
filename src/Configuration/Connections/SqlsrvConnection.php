<?php

namespace Brouwers\LaravelDoctrine\Configuration\Connections;

class SqlsrvConnection extends AbstractConnection
{
    /**
     * @var string
     */
    protected $name = 'sqlsrv';

    /**
     * @param array $config
     *
     * @return array
     */
    public function configure($config = [])
    {
        return new static ([
            'driver'   => 'pdo_sqlsrv',
            'host'     => $config['host'],
            'dbname'   => $config['database'],
            'user'     => $config['username'],
            'password' => $config['password'],
            'port'     => @$config['port']
        ]);
    }
}
