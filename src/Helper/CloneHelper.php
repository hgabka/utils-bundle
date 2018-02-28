<?php


namespace Hgabka\UtilsBundle\Helper;

use Doctrine\ORM\EntityManager;
use Hgabka\UtilsBundle\Event\DeepCloneAndSaveEvent;
use Hgabka\UtilsBundle\Event\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CloneHelper
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EntityManager            $em              The EntityManager
     * @param EventDispatcherInterface $eventDispatcher The EventDispatchInterface
     */
    public function __construct(EntityManager $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param mixed $entity
     *
     * @return mixed
     */
    public function deepCloneAndSave($entity)
    {
        $clonedEntity = clone $entity;
        $this->eventDispatcher->dispatch(Events::DEEP_CLONE_AND_SAVE, new DeepCloneAndSaveEvent($entity, $clonedEntity, $this->em));

        $this->em->persist($clonedEntity);
        $this->em->flush();

        $this->eventDispatcher->dispatch(Events::POST_DEEP_CLONE_AND_SAVE, new DeepCloneAndSaveEvent($entity, $clonedEntity, $this->em));

        return $clonedEntity;
    }
}
