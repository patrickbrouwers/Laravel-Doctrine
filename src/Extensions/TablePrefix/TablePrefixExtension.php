<?php

namespace Brouwers\LaravelDoctrine\Extensions\TablePrefix;

use Brouwers\LaravelDoctrine\Extensions\Extension;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;

class TablePrefixExtension implements Extension
{
    /**
     * @param EventManager           $manager
     * @param EntityManagerInterface $em
     * @param Reader                 $reader
     */
    public function addSubscribers(EventManager $manager, EntityManagerInterface $em, Reader $reader)
    {
        $manager->addEventListener(
            Events::loadClassMetadata,
            new TablePrefixListener(config('doctrine.connections.prefix'))
        );
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [];
    }
}
