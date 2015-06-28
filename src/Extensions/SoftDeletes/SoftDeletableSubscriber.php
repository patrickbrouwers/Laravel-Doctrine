<?php

namespace Brouwers\LaravelDoctrine\Extensions\SoftDeletes;

use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;

class SoftDeletableSubscriber implements EventSubscriber
{
    /**
     * Returns an array of events this subscriber wants to listen to.
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'onFlush'
        ];
    }

    /**
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $entityManager = $event->getEntityManager();
        $unitOfWork    = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            if ($this->isSoftDeletable($entity)) {
                $metadata     = $entityManager->getClassMetadata(get_class($entity));
                $oldDeletedAt = $metadata->getFieldValue($entity, 'deleted_at');

                if ($oldDeletedAt instanceof DateTime) {
                    continue;
                }

                $now = new DateTime;
                $metadata->setFieldValue($entity, 'deleted_at', $now);

                $entityManager->persist($entity);

                $unitOfWork->propertyChanged($entity, 'deleted_at', $oldDeletedAt, $now);

                $unitOfWork->scheduleExtraUpdate($entity, [
                    'deleted_at' => [
                        $oldDeletedAt,
                        $now
                    ]
                ]);
            }
        }
    }

    /**
     * @param $entity
     *
     * @return bool
     */
    protected function isSoftDeletable($entity)
    {
        return array_key_exists(SoftDeletes::class, class_uses_recursive(get_class($entity)));
    }
}
