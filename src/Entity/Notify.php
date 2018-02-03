<?php

namespace Hgabka\LoggerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Notify.
 *
 * @ORM\Entity(repositoryClass="Hgabka\LoggerBundle\Repository\NotifyRepository")
 * @ORM\Table(name="hg_logger_notify")
 */
class Notify
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
     * @ORM\Column(type="string", name="controller", nullable=true)
     */
    protected $controller;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="exception_class", nullable=true)
     */
    protected $exceptionClass;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="message", nullable=true)
     */
    protected $message;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="code", nullable=true)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="file", nullable=true)
     */
    protected $file;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="line", nullable=true)
     */
    protected $line;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="traces", nullable=true)
     */
    protected $traces;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="server_name", nullable=true)
     */
    protected $serverName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="redirect_url", nullable=true)
     */
    protected $redirectUrl;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="request_uri", nullable=true)
     */
    protected $requestUri;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="post", nullable=true)
     */
    protected $post;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="request", nullable=true)
     */
    protected $request;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="params", nullable=true)
     */
    protected $params;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="call_number", nullable=true)
     */
    protected $callNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="hash", nullable=true)
     */
    protected $hash;

    /**
     * @var string
     *
     * @ORM\Column(type="boolean", name="send_again", nullable=true)
     */
    protected $sendAgain = false;

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
     * @var ArrayCollection|NotifyCall[]
     *
     * @ORM\OneToMany(targetEntity="Hgabka\LoggerBundle\Entity\NotifyCall", cascade={"all"}, mappedBy="notify", orphanRemoval=true)
     *
     * @Assert\Valid()
     */
    protected $calls;

    /**
     * Notify constructor.
     */
    public function __construct()
    {
        $this->calls = new ArrayCollection();
    }

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
     * @return Notify
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return Notify
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * @return string
     */
    public function getExceptionClass(): string
    {
        return $this->exceptionClass;
    }

    /**
     * @param string $exceptionClass
     *
     * @return Notify
     */
    public function setExceptionClass($exceptionClass)
    {
        $this->exceptionClass = $exceptionClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return Notify
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     *
     * @return Notify
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param string $file
     *
     * @return Notify
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * @param int $line
     *
     * @return Notify
     */
    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * @return string
     */
    public function getTraces(): string
    {
        return $this->traces;
    }

    /**
     * @param string $traces
     *
     * @return Notify
     */
    public function setTraces($traces)
    {
        $this->traces = $traces;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     *
     * @return Notify
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

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
     * @return Notify
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;

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
     * @return Notify
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequest(): string
    {
        return $this->request;
    }

    /**
     * @param string $request
     *
     * @return Notify
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return string
     */
    public function getParams(): string
    {
        return $this->params;
    }

    /**
     * @param string $params
     *
     * @return Notify
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return int
     */
    public function getCallNumber()
    {
        return $this->callNumber;
    }

    /**
     * @param string $callNumber
     *
     * @return Notify
     */
    public function setCallNumber($callNumber)
    {
        $this->callNumber = $callNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     *
     * @return Notify
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getSendAgain(): string
    {
        return $this->sendAgain;
    }

    /**
     * @param string $sendAgain
     *
     * @return Notify
     */
    public function setSendAgain($sendAgain)
    {
        $this->sendAgain = $sendAgain;

        return $this;
    }

    /**
     * @return ArrayCollection|NotifyCall[]
     */
    public function getCalls()
    {
        return $this->calls;
    }

    /**
     * @param ArrayCollection|NotifyCall[] $calls
     *
     * @return Notify
     */
    public function setCalls($calls)
    {
        $this->calls = $calls;

        return $this;
    }

    /**
     * Add call.
     *
     * @param NotifyCall $call
     *
     * @return Notify
     */
    public function addCall(NotifyCall $call)
    {
        if (!$this->calls->contains($call)) {
            $this->calls[] = $call;

            $call->setNotify($this);
        }

        return $this;
    }

    /**
     * Remove call.
     *
     * @param NotifyCall $call
     */
    public function removeCall(NotifyCall $call)
    {
        $this->calls->removeElement($call);
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
