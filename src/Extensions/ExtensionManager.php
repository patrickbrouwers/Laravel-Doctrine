<?php

namespace Brouwers\LaravelDoctrine\Extensions;

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
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em       = $em;
        $this->evm      = $em->getEventManager();
        $this->chain    = new MappingDriverChain();
        $this->metadata = $em->getConfiguration();
        $this->reader   = $this->metadata->getMetadataDriverImpl()->getReader();
    }

    /**
     * Boot the extensions
     */
    public function boot()
    {
        foreach ($this->extensions as $extenion) {
            $this->bootExtension($extenion);
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
