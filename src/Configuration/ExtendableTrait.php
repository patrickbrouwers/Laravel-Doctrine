<?php

namespace Brouwers\LaravelDoctrine\Configuration;

use Brouwers\LaravelDoctrine\Exceptions\DriverNotRegisteredException;

trait ExtendableTrait
{
    /**
     * @var array
     */
    protected $drivers = [];

    /**
     * @var static
     */
    protected static $instance;

    /**
     * @param $driver
     */
    public function register(Driver $driver)
    {
        $this->drivers[$driver->getName()] = $driver;
    }

    /**
     * @param $name
     *
     * @throws DriverNotRegisteredException
     * @return mixed
     */
    public static function resolve($name)
    {
        if ($driver = self::getInstance()->get($name)) {
            return $driver;
        }

        throw new DriverNotRegisteredException("Driver {$name} not registered");
    }

    /**
     * @param      $driver
     * @param null $default
     *
     * @return Driver
     */
    public function get($driver, $default = null)
    {
        if (isset($this->drivers[$driver])) {
            return $this->drivers[$driver]->resolve();
        }

        return $default;
    }

    /**
     * @param          $driver
     * @param callable $callback
     */
    public static function extend($driver, $callback = null)
    {
        $class   = null;
        $manager = self::getInstance();

        if (!is_callable($callback)) {
            $class    = $callback;
            $callback = null;
        }

        $manager->register(
            $manager->transformToDriver($driver, $callback, $class)
        );
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        return static::$instance = static::$instance ?: new static();
    }

    /**
     * @return array
     */
    public static function getDrivers()
    {
        return self::getInstance()->drivers;
    }
}
