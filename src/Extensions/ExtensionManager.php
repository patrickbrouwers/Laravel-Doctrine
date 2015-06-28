<?php

namespace Brouwers\LaravelDoctrine\Extensions;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;

class ExtensionManager
{
    /**
     * @var array|Extension[]
     */
    protected $extensions = [];

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Doctrine\Common\EventManager
     */
    protected $evm;

    /**
     * @var Reader
     */
    protected $reader;

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
}
