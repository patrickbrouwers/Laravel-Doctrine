<?php

namespace Brouwers\LaravelDoctrine\Configuration\MetaData;

use Brouwers\LaravelDoctrine\Configuration\Extendable;
use Brouwers\LaravelDoctrine\Configuration\ExtendableTrait;
use Brouwers\LaravelDoctrine\Configuration\Hookable;
use Brouwers\LaravelDoctrine\Exceptions\CouldNotExtendException;
use Brouwers\LaravelDoctrine\Exceptions\DriverNotFoundException;
use Closure;
use Doctrine\ORM\Configuration;

class MetaDataManager implements Extendable, Hookable
{
    use ExtendableTrait;

    /**
     * @param array $drivers
     * @param bool  $dev
     *
     * @throws DriverNotFoundException
     * @return mixed|void
     */
    public static function registerDrivers(array $drivers = [], $dev = false)
    {
        $manager = static::getInstance();

        foreach ($drivers as $name => $driver) {
            $class = __NAMESPACE__ . '\\' . studly_case($name);

            if (class_exists($class)) {
                $driver = (new $class())->configure($driver, $dev);
                $manager->register($driver);
            } else {
                throw new DriverNotFoundException("Driver {$name} is not supported");
            }
        }
    }

    /**
     * @param         $driver
     * @param Closure $callback
     * @param null    $class
     *
     * @throws CouldNotExtendException
     * @return MetaData
     */
    public function transformToDriver($driver, Closure $callback = null, $class = null)
    {
        if ($callback) {
            $result = call_user_func($callback, $this->get($driver));

            if ($result instanceof Configuration) {
                return new CustomMetaData($result, $driver);
            }
        }

        if (class_exists($class)) {
            $result = new $class;

            if ($result instanceof MetaData) {
                $result = $result->configure();
                $result->setName($driver);

                return $result;
            }
        }

        throw new CouldNotExtendException('Expected an instance of MetaData or Doctrine\ORM\Configuration');
    }

    /**
     * @param callable $callback
     */
    public static function resolved(Closure $callback)
    {
        app('events')->listen(get_class(self::getInstance()) . ':resolved', $callback);
    }
}
