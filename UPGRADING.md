Upgrade from MessageBundle v2.0.1
=======================

 * Switch DateTime to DateTimeImmutable
 * Sent message is now automatically marked as read for the sender
 * getThread method in the Provider class doesn't mark the thread as read anymore
 * When a thread/message is marked as deleted, it's not marked as read anymore
 
Rename the following method calls:
 * getIsRead -> isRead
 * getIsDeleted -> isDeleted
 * getIsSpam -> isSpam
 * setIsRead -> setRead
 * setIsDeleted -> setDeleted
 * setIsSpam -> setSpam