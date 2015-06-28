<?php

namespace Brouwers\LaravelDoctrine\Extensions\SoftDeletes;

use Brouwers\LaravelDoctrine\Extensions\Extension;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;

class SoftDeleteableExtension implements Extension
{
    /**
     * @param EventManager           $manager
     * @param EntityManagerInterface $em
     * @param Reader                 $reader
     */
    public function addSubscribers(EventManager $manager, EntityManagerInterface $em, Reader $reader)
    {
        $manager->addEventSubscriber(
            new SoftDeletableSubscriber()
        );
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            'trashed' => TrashedFilter::class
        ];
    }
}
