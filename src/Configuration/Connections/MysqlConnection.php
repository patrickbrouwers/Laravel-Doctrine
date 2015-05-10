<?php

namespace Brouwers\LaravelDoctrine\Configuration\Connections;

class MysqlConnection extends AbstractConnection
{
    /**
     * @var string
     */
    protected $name = 'mysql';

    /**
     * @param array $config
     *
     * @return array
     */
    public function configure($config = [])
    {
        return new static ([
            'driver'      => 'pdo_mysql',
            'host'        => $config['host'],
            'dbname'      => $config['database'],
            'user'        => $config['username'],
            'password'    => $config['password'],
            'charset'     => $config['charset'],
            'port'        => @$config['port'],
            'unix_socket' => @$config['unix_socket']
        ]);
    }
}
