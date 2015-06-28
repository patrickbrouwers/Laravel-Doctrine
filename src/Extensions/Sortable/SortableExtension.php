<?php

namespace Brouwers\LaravelDoctrine\Extensions\Sortable;

use Brouwers\LaravelDoctrine\Extensions\Extension;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sortable\SortableListener;

class SortableExtension implements Extension
{
    /**
     * @param EventManager           $manager
     * @param EntityManagerInterface $em
     * @param Reader                 $reader
     */
    public function addSubscribers(EventManager $manager, EntityManagerInterface $em, Reader $reader)
    {
        $listener = new SortableListener();
        $listener->setAnnotationReader(
            $reader
        );
        $manager->addEventSubscriber($listener);
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [];
    }
}
