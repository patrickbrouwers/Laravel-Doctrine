<?php

use Brouwers\LaravelDoctrine\Configuration\Cache\CacheManager;
use Brouwers\LaravelDoctrine\Configuration\Cache\CustomCacheProvider;
use Brouwers\LaravelDoctrine\Configuration\Cache\FileCacheProvider;
use Doctrine\Common\Cache\PhpFileCache;
use Doctrine\ORM\Tools\Setup;

class CacheManagerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        CacheManager::registerDrivers([
            'file' => [
                'path' => 'path'
            ]
        ]);
    }

    public function test_register_caches()
    {
        $drivers = CacheManager::getDrivers();
        $this->assertCount(1, $drivers);
        $this->assertInstanceOf(FileCacheProvider::class, head($drivers));
    }

    public function test_cache_can_be_extended()
    {
        CacheManager::extend('file', function ($driver) {

            // Should give instance of the already registered driver
            $this->assertInstanceOf(FileCacheProvider::class, $driver);

            return $driver;
        });

        $driver = CacheManager::resolve('file');

        $this->assertInstanceOf(PhpFileCache::class, $driver->getCache());
    }

    public function test_custom_cache_can_be_set()
    {
        CacheManager::extend('custom', function () {
            return new PhpFileCache('path');
        });

        $driver = CacheManager::resolve('custom');
        $this->assertInstanceOf(CustomCacheProvider::class, $driver);
        $this->assertInstanceOf(PhpFileCache::class, $driver->getCache());
    }

    public function test_a_custom_class_can_be_returned_while_extending()
    {
        CacheManager::extend('custom2', function () {
            return new CustomCacheProvider();
        });

        $driver = CacheManager::resolve('custom2');
        $this->assertInstanceOf(CustomCacheProvider::class, $driver);
    }

    public function test_a_string_class_can_be_use_as_extend()
    {
        CacheManager::extend('custom3', CustomCacheProvider::class);

        $driver = CacheManager::resolve('custom3');
        $this->assertInstanceOf(CustomCacheProvider::class, $driver);
    }
}
