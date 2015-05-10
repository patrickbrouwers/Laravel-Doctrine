<?php

namespace Brouwers\LaravelDoctrine\Configuration\MetaData;

use Brouwers\LaravelDoctrine\Configuration\Extendable;
use Brouwers\LaravelDoctrine\Configuration\ExtendableTrait;
use Brouwers\LaravelDoctrine\Exceptions\ConfigurationNotFoundException;
use Brouwers\LaravelDoctrine\Exceptions\CouldNotExtendException;
use Closure;
use Doctrine\ORM\Configuration;

class MetaDataManager implements Extendable
{
    use ExtendableTrait;

    /**
     * @param array $drivers
     * @param bool  $dev
     *
     * @throws ConfigurationNotFoundException
     * @return mixed|void
     */
    public static function registerDrivers(array $drivers = [], $dev = false)
    {
        $manager = static::getInstance();

        foreach ($drivers as $driver => $meta) {
            $class = __NAMESPACE__ . '\\' . studly_case($driver);

            if (class_exists($class)) {
                $meta = (new $class())->configure($meta, $dev);
                $manager->register($meta);
            } else {
                throw new ConfigurationNotFoundException("Driver {$driver} is not supported");
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

            if ($result instanceof MetaData) {
                $result->setName($driver);

                return $result;
            }

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
}
