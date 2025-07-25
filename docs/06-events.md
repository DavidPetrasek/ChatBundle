[Back to index](00-index.md)

Events
======

You can see definitions and explanations of each event as defined in `Event\FOSMessageEvents`:

```php
<?php
namespace FOS\ChatBundle\Event;

/**
 * Declares all events thrown in the MessageBundle.
 */
final class FOSMessageEvents
{
    /**
     * The POST_SEND event occurs after a message has been sent
     * The event is an instance of FOS\ChatBundle\Event\MessageEvent.
     */
    const string POST_SEND = 'fos_chat.post_send';

    /**
     * The POST_DELETE event occurs after a thread has been marked as deleted
     * The event is an instance of FOS\ChatBundle\Event\ThreadEvent.
     */
    const string POST_DELETE = 'fos_chat.post_delete';

    /**
     * The POST_UNDELETE event occurs after a thread has been marked as undeleted
     * The event is an instance of FOS\ChatBundle\Event\ThreadEvent.
     */
    const string POST_UNDELETE = 'fos_chat.post_undelete';

    /**
     * The POST_READ event occurs after a thread has been marked as read
     * The event is an instance of FOS\ChatBundle\Event\ReadableEvent.
     */
    const string POST_READ = 'fos_chat.post_read';

    /**
     * The POST_UNREAD event occurs after a thread has been unread
     * The event is an instance of FOS\ChatBundle\Event\ReadableEvent.
     */
    const string POST_UNREAD = 'fos_chat.post_unread';
}
```