<?php

namespace Brouwers\LaravelDoctrine;

use Brouwers\LaravelDoctrine\Configuration\Cache\CacheManager;
use Brouwers\LaravelDoctrine\Configuration\Connections\ConnectionManager;
use Brouwers\LaravelDoctrine\Configuration\MetaData\MetaDataManager;
use Brouwers\LaravelDoctrine\Console\ClearMetadataCacheCommand;
use Brouwers\LaravelDoctrine\Console\ClearQueryCacheCommand;
use Brouwers\LaravelDoctrine\Console\ClearResultCacheCommand;
use Brouwers\LaravelDoctrine\Console\EnsureProductionSettingsCommand;
use Brouwers\LaravelDoctrine\Console\GenerateProxiesCommand;
use Brouwers\LaravelDoctrine\Console\InfoCommand;
use Brouwers\LaravelDoctrine\Console\SchemaCreateCommand;
use Brouwers\LaravelDoctrine\Console\SchemaDropCommand;
use Brouwers\LaravelDoctrine\Console\SchemaUpdateCommand;
use Brouwers\LaravelDoctrine\Console\SchemaValidateCommand;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Illuminate\Support\ServiceProvider;

class DoctrineServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Boot service provider.
     */
    public function boot()
    {
        $this->publishes([
            $this->getConfigPath() => config_path('doctrine.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->setupCache();
        $this->mergeConfig();
        $this->setupMetaData();
        $this->setupConnection();
        $this->setupEntityManager();
        $this->registerClassMetaDataFactory();
        $this->registerConsoleCommands();
    }

    /**
     * Merge config
     */
    protected function mergeConfig()
    {
        $this->mergeConfigFrom(
            $this->getConfigPath(), 'doctrine'
        );

        $this->config = $this->app['config']['doctrine'];
    }

    /**
     * Setup the entity manager
     */
    protected function setupEntityManager()
    {
        // Bind EntityManager as singleton
        $this->app->singleton('em', function () {
            return EntityManager::create(
                ConnectionManager::resolve($this->config['connections']['default']),
                MetaDataManager::resolve($this->config['meta']['driver'])
            );
        });

        $this->app->alias('em', 'Doctrine\ORM\EntityManager');
        $this->app->alias('em', 'Doctrine\ORM\EntityManagerInterface');
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

    /**
     * Setup the Class metadata factory
     */
    protected function registerClassMetaDataFactory()
    {
        $this->app->singleton(ClassMetadataFactory::class, function ($app) {
            return $app['em']->getMetadataFactory();
        });
    }

    /**
     * Register console commands
     */
    protected function registerConsoleCommands()
    {
        $this->commands([
            InfoCommand::class,
            SchemaCreateCommand::class,
            SchemaUpdateCommand::class,
            SchemaDropCommand::class,
            SchemaValidateCommand::class,
            ClearMetadataCacheCommand::class,
            ClearResultCacheCommand::class,
            ClearQueryCacheCommand::class,
            EnsureProductionSettingsCommand::class,
            GenerateProxiesCommand::class
        ]);
    }

    /**
     * @return string
     */
    protected function getConfigPath()
    {
        return __DIR__ . '/../config/doctrine.php';
    }
}
