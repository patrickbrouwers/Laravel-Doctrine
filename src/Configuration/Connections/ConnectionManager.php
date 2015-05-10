<?php

namespace Brouwers\LaravelDoctrine\Configuration\Connections;

use Brouwers\LaravelDoctrine\Configuration\Extendable;
use Brouwers\LaravelDoctrine\Configuration\ExtendableTrait;
use Brouwers\LaravelDoctrine\Exceptions\CouldNotExtendException;
use Closure;
use Doctrine\DBAL\ConnectionException;

class ConnectionManager implements Extendable
{
    use ExtendableTrait;

    /**
     * @param $connections
     *
     * @throws ConnectionException
     */
    public static function registerConnections($connections)
    {
        $connector = static::getInstance();

        foreach ($connections as $driver => $connection) {
            $class = __NAMESPACE__ . '\\' . studly_case($driver) . 'Connection';

            if (class_exists($class)) {
                $connection = (new $class())->configure($connection);
                $connector->register($connection);
            } else {
                throw new ConnectionException("Connection {$driver} is not supported");
            }
        }
    }

    /**
     * @param         $driver
     * @param Closure $callback
     * @param null    $class
     *
     * @throws CouldNotExtendException
     * @return Connection
     */
    public function transformToDriver($driver, Closure $callback = null, $class = null)
    {
        if ($callback) {
            $result = call_user_func($callback, $this->get($driver));

            if ($result instanceof Connection) {
                $result->setName($driver);

                return $result;
            }

            return new CustomConnection($result, $driver);
        }

        if (class_exists($class)) {
            $result = new $class;

            if ($result instanceof Connection) {
                $result = $result->configure();
                $result->setName($driver);

                return $result;
            }
        }

        throw new CouldNotExtendException('Expected an instance of Connection or Doctrine\ORM\Configuration');
    }
}
