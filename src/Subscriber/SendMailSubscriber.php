<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 30/10/2020
 * Time: 08:33
 */

namespace App\Subscriber;


use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SendMailSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onSendMail', 9]
        ];
    }

    public function onSendMail(ViewEvent $event)
    {

    }

}