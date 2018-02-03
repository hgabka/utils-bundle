<?php

namespace Hgabka\LoggerBundle\EventListener;

use Hgabka\LoggerBundle\Event\LogActionEvent;
use Hgabka\LoggerBundle\Logger\ActionLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ActionLogSubscriber implements EventSubscriberInterface
{
    /** @var ActionLogger */
    protected $logger;

    /**
     * ActionLogSubscriber constructor.
     *
     * @param ActionLogger $logger
     */
    public function __construct(ActionLogger $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            LogActionEvent::EVENT_START => 'onStart',
            LogActionEvent::EVENT_DONE => 'onDone',
            LogActionEvent::EVENT_UPDATE => 'onUpdate',
            LogActionEvent::EVENT_LOG => 'onLog',
         //   LogActionEvent::EVENT_FORM => 'onAddHeaders',
        ];
    }

    /**
     * @param LogActionEvent $event
     */
    public function onStart(LogActionEvent $event)
    {
        $this->logger->start($event->getType(), $event->getParameters());
    }

    /**
     * @param LogActionEvent $event
     */
    public function onUpdate(LogActionEvent $event)
    {
        $this->logger->update($event->getParameters(), $event->getPriority());
    }

    /**
     * @param LogActionEvent $event
     */
    public function onDone(LogActionEvent $event)
    {
        $this->logger->done();
    }

    /**
     * @param LogActionEvent $event
     */
    public function onLog(LogActionEvent $event)
    {
        $this->logger->log($event->getType(), $event->getParameters(), $event->getPriority());
    }

    /**
     * @param MailerEvent $event
     */
    public function onMailSent(MailerEvent $event)
    {
        if ('mailer_send' !== $this->strategy) {
            $this->logger->logMessage($event);
        }
    }

    /**
     * @param MailerEvent $event
     */
    public function onAddHeaders(MailerEvent $event)
    {
        $event->setReturnValue($event->getParameter('configHeaders'));
    }
}
