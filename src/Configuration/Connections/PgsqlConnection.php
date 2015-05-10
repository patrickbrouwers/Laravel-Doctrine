<?php

namespace Brouwers\LaravelDoctrine\Configuration\Connections;

class PgsqlConnection extends AbstractConnection
{
    /**
     * @var string
     */
    protected $name = 'pgsql';

    /**
     * @param array $config
     *
     * @return array
     */
    public function configure($config = [])
    {
        return new static ([
            'driver'   => 'pdo_pgsql',
            'host'     => $config['host'],
            'dbname'   => $config['database'],
            'user'     => $config['username'],
            'password' => $config['password'],
            'charset'  => $config['charset'],
            'port'     => @$config['port'],
            'sslmode'  => @$config['sslmode']
        ]);
    }
}
