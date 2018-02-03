<?php

namespace Hgabka\LoggerBundle\Traits;

use Hgabka\LoggerBundle\Event\LogActionEvent;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

trait LoggableTrait
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @required
     *
     * @param BreadcrumbManager $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    protected function logStart($type, $params = [], $priority = null)
    {
        $priority = $priority ?? Logger::getLevelName(Logger::INFO);

        $event = new LogActionEvent();
        $event
            ->setType($type)
            ->setParameters($params)
            ->setPriority($priority)
        ;

        $this->dispatcher->dispatch(LogActionEvent::EVENT_START, $event);
    }

    protected function logDone()
    {
        $event = new LogActionEvent();

        $this->dispatcher->dispatch(LogActionEvent::EVENT_DONE, $event);
    }

    protected function logUpdate()
    {
        $event = new LogActionEvent();

        $this->dispatcher->dispatch(LogActionEvent::EVENT_UPDATE, $event);
    }

    protected function logError()
    {
        $event = new LogActionEvent();
        $event
            ->setParameters(null)
            ->setPriority(Logger::getLevelName(Logger::ERROR))
        ;

        $this->dispatcher->dispatch(LogActionEvent::EVENT_UPDATE, $event);
        $this->dispatcher->dispatch(LogActionEvent::EVENT_DONE, $event);
    }

    protected function actionLog($type, $params = [], $priority = null)
    {
        $this->logStart($type, $params, $priority);
        $this->logDone();
    }
}