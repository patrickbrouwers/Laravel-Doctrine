<?php

namespace Brouwers\LaravelDoctrine;

use Brouwers\LaravelDoctrine\Auth\DoctrineUserProvider;
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
use Brouwers\LaravelDoctrine\Exceptions\ExtensionNotFound;
use Brouwers\LaravelDoctrine\Extensions\ExtensionManager;
use Brouwers\LaravelDoctrine\Validation\DoctrinePresenceVerifier;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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
        $this->extendAuthManager();

        // Boot the extension manager
        $this->app->make(ExtensionManager::class)->boot();

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
        $this->registerExtensions();
        $this->registerCustomTypes();
        $this->registerPresenceVerifier();
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

        $this->app->alias('em', EntityManager::class);
        $this->app->alias('em', EntityManagerInterface::class);
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

        MetaDataManager::resolved(function (Configuration $configuration) {
            $configuration->setDefaultRepositoryClassName($this->config['repository']);
            $configuration->setAutoGenerateProxyClasses($this->config['meta']['proxies']['auto_generate']);

            if ($namespace = $this->config['meta']['proxies']['namespace']) {
                $configuration->setProxyNamespace($namespace);
            }

            if ($this->config['debugbar'] === true) {
                $debugStack = new DebugStack();
                $configuration->setSQLLogger($debugStack);
                $this->app['debugbar']->addCollector(
                    new DoctrineCollector($debugStack)
                );
            }

            $configuration->setCustomDatetimeFunctions($this->config['custom_datetime_functions']);
            $configuration->setCustomNumericFunctions($this->config['custom_numeric_functions']);
            $configuration->setCustomStringFunctions($this->config['custom_string_functions']);
        });
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
     * Register doctrine extensions
     */
    protected function registerExtensions()
    {
        // Bind extension manager as singleton,
        // so user can call it and add own extensions
        $this->app->singleton(ExtensionManager::class, function ($app) {
            $manager = new ExtensionManager($this->app['em']);

            if ($this->config['gedmo_extensions']['enabled']) {
                $manager->enableGedmoExtensions(
                    $this->config['meta']['namespace'],
                    $this->config['gedmo_extensions']['all_mappings']
                );
            }

            return $manager;
        });

        // Register the extensions
        foreach ($this->config['extensions'] as $extension) {
            if (!class_exists($extension)) {
                throw new ExtensionNotFound("Extension {$extension} not found");
            }

            $this->app->make(ExtensionManager::class)->register(
                $this->app->make($extension)
            );
        }
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function registerCustomTypes()
    {
        foreach ($this->config['custom_types'] as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            } else {
                Type::overrideType($name, $class);
            }
        }
    }

    /**
     * Register the validation presence verifier
     */
    protected function registerPresenceVerifier()
    {
        $this->app->singleton('validation.presence', DoctrinePresenceVerifier::class);
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
     * Extend the auth manager
     */
    protected function extendAuthManager()
    {
        $this->app['Illuminate\Auth\AuthManager']->extend('doctrine', function ($app) {
            return new DoctrineUserProvider(
                $app['Illuminate\Contracts\Hashing\Hasher'],
                $app['em'],
                $app['config']['auth.model']
            );
        });
    }

    /**
     * @return string
     */
    protected function getConfigPath()
    {
        return __DIR__ . '/../config/doctrine.php';
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return [
            'em',
            EntityManager::class,
            ClassMetadataFactory::class,
            EntityManagerInterface::class
        ];
    }
}
