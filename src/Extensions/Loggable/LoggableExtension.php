<?php

namespace Brouwers\LaravelDoctrine\Extensions\Loggable;

use Brouwers\LaravelDoctrine\Extensions\Extension;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\LoggableListener;

class LoggableExtension implements Extension
{
    /**
     * @param EventManager           $manager
     * @param EntityManagerInterface $em
     * @param Reader                 $reader
     */
    public function addSubscribers(EventManager $manager, EntityManagerInterface $em, Reader $reader)
    {
        $listener = new LoggableListener;
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
