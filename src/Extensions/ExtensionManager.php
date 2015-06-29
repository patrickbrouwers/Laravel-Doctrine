<?php

namespace Brouwers\LaravelDoctrine\Extensions;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\DoctrineExtensions;

class ExtensionManager
{
    /**
     * @var array|Extension[]
     */
    protected $extensions = [];

    /**
     * @var arary
     */
    protected $gedmo = [
        'enabled' => false
    ];

    /**
     * @var MappingDriverChain
     */
    protected $chain;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Doctrine\Common\EventManager
     */
    protected $evm;

    /**
     * @var \Doctrine\ORM\Configuration
     */
    protected $metadata;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Boot the extensions
     */
    public function boot()
    {
        foreach ($this->registry->getManagers() as $em) {
            $this->em       = $em;
            $this->chain    = new MappingDriverChain();
            $this->evm      = $this->em->getEventManager();
            $this->metadata = $this->em->getConfiguration();
            $this->reader   = method_exists($this->metadata->getMetadataDriverImpl(), 'getReader')
                ? $this->metadata->getMetadataDriverImpl()->getReader()
                : false;

            if ($this->gedmo['enabled']) {
                $this->bootGedmoExtensions($this->gedmo['namespace'], $this->gedmo['all']);
            }

            foreach ($this->extensions as $extenion) {
                $this->bootExtension($extenion);
            }
        }
    }

    /**
     * @param Extension $extension
     */
    public function register(Extension $extension)
    {
        $this->extensions[] = $extension;
    }

    /**
     * @param Extension $extension
     */
    public function bootExtension(Extension $extension)
    {
        $extension->addSubscribers($this->evm, $this->em, $this->reader);

        foreach ($extension->getFilters() as $name => $filter) {
            $this->metadata->addFilter($name, $filter);
            $this->em->getFilters()->enable($name);
        }
    }

    /**
     * Enable Gedmo Doctrine Extensions
     *
     * @param string $namespace
     * @param bool   $all
     */
    public function enableGedmoExtensions($namespace = 'App', $all = true)
    {
        $this->gedmo = [
            'enabled'   => true,
            'namespace' => $namespace,
            'all'       => $all
        ];
    }

    /**
     * Enable Gedmo Doctrine Extensions
     *
     * @param string $namespace
     * @param bool   $all
     */
    public function bootGedmoExtensions($namespace = 'App', $all = true)
    {
        if ($all) {
            DoctrineExtensions::registerMappingIntoDriverChainORM(
                $this->chain,
                $this->reader
            );
        } else {
            DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
                $this->chain,
                $this->reader
            );
        }

        $this->chain->addDriver($this->metadata->getMetadataDriverImpl(), $namespace);
        $this->metadata->setMetadataDriverImpl($this->chain);
    }
}
