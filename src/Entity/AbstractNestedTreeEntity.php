<?php

namespace Hgabka\UtilsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class AbstractNestedTreeEntity implements NestedEntityInterface, EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id The unique identifier
     *
     * @return AbstractNestedTreeEntity
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return bool
     */
    public function canHaveChildren()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isDeleteable()
    {
        return null === $this->getParent();
    }

    abstract public function getParent();

    abstract public function getLeft();

    abstract public function getRight();

    abstract public function getChildren();
}
