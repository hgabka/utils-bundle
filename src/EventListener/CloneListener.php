<?php

namespace Hgabka\UtilsBundle\EventListener;

use Hgabka\UtilsBundle\Entity\DeepCloneInterface;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;

/**
 * This listener will make sure the id isn't copied for AbstractEntities.
 */
class CloneListener
{
    /**
     * @param DeepCloneAndSaveEvent $event
     */
    public function onDeepCloneAndSave(DeepCloneAndSaveEvent $event)
    {
        $clonedEntity = $event->getClonedEntity();

        if (method_exists($clonedEntity, 'setId')) {
            $clonedEntity->setId(null);
        }

        if ($clonedEntity instanceof DeepCloneInterface) {
            $clonedEntity->deepClone();
        }
    }
}
