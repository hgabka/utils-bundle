<?php

namespace Hgabka\LoggerBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class LogActionEvent extends Event
{
    const EVENT_START = 'hgabka_logger.log_start';
    const EVENT_DONE = 'hgabka_logger.log_done';
    const EVENT_UPDATE = 'hgabka_logger.log_update';
    const EVENT_LOG = 'hgabka_logger.log_log';
    const EVENT_FORM = 'hgabka_logger.log_form';

    /** @var string */
    protected $type;

    /** @var array */
    protected $parameters;

    /** @var string */
    protected $priority;

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
     * @return LogActionEvent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     *
     * @return LogActionEvent
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param string $priority
     *
     * @return LogActionEvent
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }
}
