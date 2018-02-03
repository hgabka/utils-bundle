<?php

namespace Hgabka\LoggerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * LogAction.
 *
 * @ORM\Entity
 * @ORM\Table(name="hg_logger_log_action")
 */
class LogAction
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
     * @ORM\Column(type="string", name="session_id", nullable=true)
     */
    protected $sessionId;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="time", nullable=true)
     */
    protected $time;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="end_time", nullable=true)
     */
    protected $endTime;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="controller", nullable=true)
     */
    protected $controller;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="description", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="request_uri", nullable=true)
     */
    protected $requestUri;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="success", nullable=true)
     */
    protected $success;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="client_ip", nullable=true)
     */
    protected $clientIp;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="user_agent", nullable=true)
     */
    protected $userAgent;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="log_type", nullable=true)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="method", nullable=true)
     */
    protected $method;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="post", nullable=true)
     */
    protected $post;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="request_attributes", nullable=true)
     */
    protected $requestAttributes;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, name="priority", nullable=true)
     */
    protected $priority;

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
     * @return LogAction
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
     * @return LogAction
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
     * @return LogAction
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     *
     * @return LogAction
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTime
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     *
     * @return LogAction
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime(): \DateTime
    {
        return $this->endTime;
    }

    /**
     * @param \DateTime $endTime
     *
     * @return LogAction
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     *
     * @return LogAction
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return LogAction
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestUri(): string
    {
        return $this->requestUri;
    }

    /**
     * @param string $requestUri
     *
     * @return LogAction
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     *
     * @return LogAction
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientIp(): string
    {
        return $this->clientIp;
    }

    /**
     * @param string $clientIp
     *
     * @return LogAction
     */
    public function setClientIp($clientIp)
    {
        $this->clientIp = $clientIp;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     *
     * @return LogAction
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return LogAction
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * @param string $priority
     *
     * @return LogAction
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return LogAction
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestAttributes(): string
    {
        return $this->requestAttributes;
    }

    /**
     * @param string $requestAttributes
     *
     * @return LogAction
     */
    public function setRequestAttributes($requestAttributes)
    {
        $this->requestAttributes = $requestAttributes;

        return $this;
    }

    /**
     * @return string
     */
    public function getPost(): string
    {
        return $this->post;
    }

    /**
     * @param string $post
     *
     * @return LogAction
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return LogAction
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

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
}
