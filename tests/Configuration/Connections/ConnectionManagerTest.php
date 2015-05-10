<?php

use Brouwers\LaravelDoctrine\Configuration\Connections\ConnectionManager;
use Brouwers\LaravelDoctrine\Configuration\Connections\CustomConnection;
use Brouwers\LaravelDoctrine\Configuration\Connections\MysqlConnection;

class ConnectionManagerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        ConnectionManager::registerConnections([
            'mysql' => [
                'host'     => 'host',
                'database' => 'database',
                'username' => 'username',
                'password' => 'password',
                'charset'  => 'charset',
            ]
        ]);
    }

    public function test_register_connections()
    {
        $drivers = ConnectionManager::getDrivers();
        $this->assertCount(1, $drivers);
        $this->assertInstanceOf(MysqlConnection::class, head($drivers));
    }

    public function test_connection_can_be_extended()
    {
        ConnectionManager::extend('mysql', function ($driver) {

            // Should give instance of the already registered driver
            $this->assertInstanceOf(MysqlConnection::class, $driver);

            return [
                'host'     => 'host',
                'database' => 'database',
                'username' => 'username2',
                'password' => 'password',
                'charset'  => 'charset',
            ];
        });

        $driver   = ConnectionManager::resolve('mysql');
        $settings = $driver->getSettings();

        $this->assertEquals('username2', $settings['username']);
    }

    public function test_custom_connection_can_be_set()
    {
        ConnectionManager::extend('custom', function () {
            return [
                'host'     => 'host',
                'database' => 'database',
                'username' => 'username2',
                'password' => 'password',
                'charset'  => 'charset',
            ];
        });

        $driver = ConnectionManager::resolve('custom');
        $this->assertInstanceOf(CustomConnection::class, $driver);
    }

    public function test_a_custom_class_can_be_returned_while_extending()
    {
        ConnectionManager::extend('custom2', function () {
            return new CustomConnection();
        });

        $driver = ConnectionManager::resolve('custom2');
        $this->assertInstanceOf(CustomConnection::class, $driver);
    }

    public function test_a_string_class_can_be_use_as_extend()
    {
        ConnectionManager::extend('custom3', CustomConnection::class);

        $driver = ConnectionManager::resolve('custom3');
        $this->assertInstanceOf(CustomConnection::class, $driver);
    }
}
