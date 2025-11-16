<?php

namespace FOS\ChatBundle\Event;

/**
 * Declares all events thrown in the ChatBundle.
 */
final class FOSChatEvents
{
    /**
     * The POST_SEND event occurs after a message has been sent
     * The event is an instance of FOS\ChatBundle\Event\MessageEvent.
     * @var string
     */
    const POST_SEND = 'fos_chat.post_send';

    /**
     * The POST_DELETE event occurs after a thread has been marked as deleted
     * The event is an instance of FOS\ChatBundle\Event\ThreadEvent.
     * @var string
     */
    const POST_DELETE = 'fos_chat.post_delete';

    /**
     * The POST_UNDELETE event occurs after a thread has been marked as undeleted
     * The event is an instance of FOS\ChatBundle\Event\ThreadEvent.
     * @var string
     */
    const POST_UNDELETE = 'fos_chat.post_undelete';

    /**
     * The POST_READ event occurs after a thread has been marked as read
     * The event is an instance of FOS\ChatBundle\Event\ReadableEvent.
     * @var string
     */
    const POST_READ = 'fos_chat.post_read';

    /**
     * The POST_UNREAD event occurs after a thread has been unread
     * The event is an instance of FOS\ChatBundle\Event\ReadableEvent.
     * @var string
     */
    const POST_UNREAD = 'fos_chat.post_unread';
}
