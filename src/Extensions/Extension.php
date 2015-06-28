<?php

namespace Brouwers\LaravelDoctrine\Extensions;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;

interface Extension
{
    /**
     * @param EventManager           $manager
     * @param EntityManagerInterface $em
     * @param Reader                 $reader
     *
     * @return
     */
    public function addSubscribers(EventManager $manager, EntityManagerInterface $em, Reader $reader);

    /**
     * @return array
     */
    public function getFilters();
}
