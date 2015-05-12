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
     * @var array
     */
    protected $config;

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

        $this->config = $this->app['config']['doctrine'];
    }

    /**
     * Setup the entity manager
     */
    protected function setupEntityManager()
    {
        // Bind EntityManager as singleton
        $this->app->singleton('Doctrine\ORM\EntityManager', function () {
            return EntityManager::create(
                ConnectionManager::resolve($this->config['connections']['default']),
                MetaDataManager::resolve($this->config['meta']['driver'])
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
            $this->app['config']['database']['connections']
        );
    }

    /**
     * Register the meta data drivers
     */
    protected function setupMetaData()
    {
        MetaDataManager::registerDrivers(
            $this->config['meta']['drivers'],
            $this->config['dev']
        );
    }

    /**
     * Register the cache drivers
     */
    protected function setupCache()
    {
        CacheManager::registerDrivers(
            $this->app['config']['cache']['stores']
        );
    }
}
