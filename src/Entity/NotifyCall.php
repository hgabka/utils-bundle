<?php

namespace Hgabka\LoggerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * NotifyCall.
 *
 * @ORM\Entity
 * @ORM\Table(name="hg_logger_notify_call")
 */
class NotifyCall
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Notify
     *
     * @ORM\ManyToOne(targetEntity="Hgabka\LoggerBundle\Entity\Notify", inversedBy="calls", cascade={"persist"})
     * @ORM\JoinColumn(name="notify_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $notify;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="server", nullable=true)
     */
    protected $server;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", name="updated_at")
     */
    protected $updatedAt;

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
     * @return NotifyCall
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Sets createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return NotifyCall
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \Hgabka\LoggerBundle\Entity\Notify
     */
    public function getNotify(): \Hgabka\LoggerBundle\Entity\Notify
    {
        return $this->notify;
    }

    /**
     * @param \Hgabka\LoggerBundle\Entity\Notify $notify
     *
     * @return NotifyCall
     */
    public function setNotify($notify)
    {
        $this->notify = $notify;

        return $this;
    }

    /**
     * @return string
     */
    public function getServer(): string
    {
        return $this->server;
    }

    /**
     * @param string $server
     *
     * @return NotifyCall
     */
    public function setServer($server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Returns createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return NotifyCall
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Returns updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
