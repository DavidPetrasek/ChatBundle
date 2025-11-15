[Back to index](00-index.md)

Events
======

- POST_SEND
- POST_DELETE
- POST_UNDELETE
- POST_READ
- POST_UNREAD


Example usage
```php
<?php
namespace App\EventSubscriber;

use FOS\ChatBundle\Event\FOSMessageEvents;
use FOS\ChatBundle\Event\MessageEvent;
use FOS\ChatBundle\Event\ReadableEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChatBundleSubscriber implements EventSubscriberInterface
{
    public function afterSend(MessageEvent $event): void
    {
        // ...
    }

    public function afterRead(ReadableEvent $event): void
    {
        // ...
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FOSMessageEvents::POST_SEND => 'afterSend',
            FOSMessageEvents::POST_READ => 'afterRead'
        ];
    }
}
```