<?php

namespace Hgabka\UtilsBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Hgabka\UtilsBundle\Helper\ClassLookup;
use Hgabka\UtilsBundle\Repository\AclChangesetRepository;

/**
 * An Acl changeset will be added to the queue whenever a change is made to the permissions. The {@link ApplyAclCommand}
 * will execute these changesets and change their status when finished.
 *
 * @ORM\Entity(repositoryClass="Hgabka\UtilsBundle\Repository\AclChangesetRepository")
 * @ORM\Table(name="hg_utils_acl_changesets", indexes={@ORM\Index(name="idx_acl_changeset_ref", columns={"ref_id", "ref_entity_name"})})
 * @ORM\HasLifecycleCallbacks()
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
#[ORM\Entity(repositoryClass: AclChangesetRepository::class)]
#[ORM\Table(name: 'hg_utils_acl_changesets')]
#[ORM\Index(name: 'idx_acl_changeset_ref', columns: ['ref_id', 'ref_entity_name'])]
#[ORM\HasLifecycleCallbacks]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class AclChangeset
{
    /**
     * This changeset still needs to be applied.
     */
    public const STATUS_NEW = 0;

    /**
     * This changeset is currently being applied.
     */
    public const STATUS_RUNNING = 1;

    /**
     * This changeset is applied.
     */
    public const STATUS_FINISHED = 2;

    /**
     * Something went wrong while applying the changeset.
     */
    public const STATUS_FAILED = 3;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'id')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    /**
     * @ORM\Column(type="bigint", name="ref_id")
     */
    #[ORM\Column(name: 'ref_id', type: 'bigint')]
    protected ?int $refId = null;

    /**
     * @ORM\Column(type="string", name="ref_entity_name")
     */
    #[ORM\Column(name: 'ref_entity_name', type: 'string')]
    protected ?string $refEntityName = null;

    /**
     * The doctrine metadata is set dynamically in Hgabka\UtilsBundle\EventListener\MappingListener.
     */
    protected $user;

    /**
     * @ORM\Column(type="array")
     */
    #[ORM\Column(name: 'changeset', type: 'array')]
    protected ?array $changeset = null;

    /**
     * @ORM\Column(type="integer", name="pid", nullable=true)
     */
    #[ORM\Column(name: 'pid', type: 'integer', nullable: true)]
    protected ?int $pid = null;

    /**
     * @ORM\Column(type="integer", name="status")
     */
    #[ORM\Column(name: 'status', type: 'integer')]
    protected ?int $status = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    #[ORM\Column(name: 'created', type: 'datetime', nullable: true)]
    protected ?DateTime $created = null;

    /**
     * @ORM\Column(name="last_modified", type="datetime", nullable=true)
     */
    #[ORM\Column(name: 'last_modified', type: 'datetime', nullable: true)]
    protected ?DateTime $lastModified = null;

    /**
     * Constructor, sets default status to STATUS_NEW & timestamps to current datetime.
     */
    public function __construct()
    {
        $this->status = self::STATUS_NEW;
        $this->lastModified = $this->created = new DateTime('now');
    }

    /**
     * @return mixed
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setChangeset(?array $changeset): self
    {
        $this->changeset = $changeset;

        return $this;
    }

    public function getChangeset(): ?array
    {
        return $this->changeset;
    }

    public function setCreated(?DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    public function setLastModified(?DateTime $lastModified): self
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    public function getLastModified(): ?DateTime
    {
        return $this->lastModified;
    }

    public function getRefId(): ?int
    {
        return $this->refId;
    }

    public function getRefEntityName(): ?string
    {
        return $this->refEntityName;
    }

    public function setRef(object $entity): self
    {
        if (method_exists($entity, 'getId')) {
            $this->setRefId($entity->getId());
            $this->setRefEntityName(ClassLookup::getClass($entity));
        }

        return $this;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;
        $this->setLastModified(new DateTime('now'));

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setPid(?int $pid): self
    {
        $this->pid = $pid;

        return $this;
    }

    public function getPid(): ?int
    {
        return $this->pid;
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    protected function setRefId(?int $refId): self
    {
        $this->refId = $refId;

        return $this;
    }

    protected function setRefEntityName(?string $refEntityName): self
    {
        $this->refEntityName = $refEntityName;

        return $this;
    }
}
