<?php

namespace Hgabka\LoggerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * LogColumn.
 *
 * @ORM\Entity
 * @ORM\Table(name="hg_logger_log_column")
 */
class LogColumn
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="ident", nullable=true)
     */
    protected $ident;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="user_id", nullable=true)
     */
    protected $userId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="table_name", nullable=true)
     */
    protected $table;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="entity_class", nullable=true)
     */
    protected $class;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="column_name", nullable=true)
     */
    protected $column;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="field_name", nullable=true)
     */
    protected $field;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="foreign_id", nullable=true)
     */
    protected $foreignId;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="old_value", nullable=true)
     */
    protected $oldValue;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="new_value", nullable=true)
     */
    protected $newValue;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="mod_type", nullable=true)
     */
    protected $modType;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="entity_data", nullable=true)
     */
    protected $data;

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
     * @return LogColumn
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdent(): string
    {
        return $this->ident;
    }

    /**
     * @param string $ident
     *
     * @return LogColumn
     */
    public function setIdent($ident)
    {
        $this->ident = $ident;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return LogColumn
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     *
     * @return LogColumn
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return LogColumn
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * @param string $column
     *
     * @return LogColumn
     */
    public function setColumn($column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return LogColumn
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return string
     */
    public function getForeignId(): string
    {
        return $this->foreignId;
    }

    /**
     * @param string $foreignId
     *
     * @return LogColumn
     */
    public function setForeignId($foreignId)
    {
        $this->foreignId = $foreignId;

        return $this;
    }

    /**
     * @return string
     */
    public function getOldValue(): string
    {
        return $this->oldValue;
    }

    /**
     * @param string $oldValue
     *
     * @return LogColumn
     */
    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewValue(): string
    {
        return $this->newValue;
    }

    /**
     * @param string $newValue
     *
     * @return LogColumn
     */
    public function setNewValue($newValue)
    {
        $this->newValue = $newValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getModType(): string
    {
        return $this->modType;
    }

    /**
     * @param string $modType
     *
     * @return LogColumn
     */
    public function setModType($modType)
    {
        $this->modType = $modType;

        return $this;
    }

    /**
     * Sets createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

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
     * @return string
     */
    public function getServerName(): string
    {
        return $this->serverName;
    }

    /**
     * @param string $serverName
     *
     * @return Notify
     */
    public function setServerName($serverName)
    {
        $this->serverName = $serverName;

        return $this;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     *
     * @return LogColumn
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Sets updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return $this
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
