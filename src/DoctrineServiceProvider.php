<?php

namespace Brouwers\LaravelDoctrine;

use Brouwers\LaravelDoctrine\Configuration\Cache\CacheManager;
use Brouwers\LaravelDoctrine\Configuration\Connections\ConnectionManager;
use Brouwers\LaravelDoctrine\Configuration\MetaData\MetaDataManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Illuminate\Support\ServiceProvider;

class DoctrineServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
        $this->setupCache();
        $this->setupMetaData();
        $this->setupConnection();
        $this->setupEntityManager();
    }

    /**
     * Merge config
     */
    protected function mergeConfig()
    {
        $path = __DIR__ . '/../config/doctrine.php';

        $this->publishes([
            $path => config_path('doctrine.php'),
        ]);

        $this->mergeConfigFrom(
            $path, 'doctrine'
        );
    }

    /**
     * Setup the entity manager
     */
    protected function setupEntityManager()
    {
        // Bind EntityManager as singleton
        $this->app->singleton('Doctrine\ORM\EntityManager', function () {
            return EntityManager::create(
                ConnectionManager::resolve($this->app['config']['database.default'])->getSettings(),
                MetaDataManager::resolve($this->app['config']['doctrine.meta.driver'])->getConfig()
            );
        });

        // Bind to interface
        $this->app->bind('Doctrine\ORM\EntityManagerInterface', 'Doctrine\ORM\EntityManager');
    }

    /**
     * Register the connections
     * @return array
     */
    protected function setupConnection()
    {
        ConnectionManager::registerConnections(
            $this->app['config']['database.connections']
        );
    }

    /**
     * Register the meta data drivers
     */
    protected function setupMetaData()
    {
        MetaDataManager::registerDrivers(
            $this->app['config']['doctrine.meta.drivers'],
            $this->app['config']['app.debug']
        );
    }

    /**
     * Register the cache drivers
     */
    protected function setupCache()
    {
        CacheManager::registerDrivers(
            $this->app['config']['cache.stores']
        );
    }
}
