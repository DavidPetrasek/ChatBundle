How to upgrade from MessageBundle v2.0.1
=======================

- Switch DateTime to DateTimeImmutable
 
Rename the following method calls:
- getIsRead -> isRead
- getIsDeleted -> isDeleted
- getIsSpam -> isSpam
- setIsRead -> setRead
- setIsDeleted -> setDeleted
- setIsSpam -> setSpam

New features / changes:
- Installer
- Automatic reply
- Participant's status
- Track when something was read or deleted
- Sent message is now automatically marked as read for the sender
- `getThread` method in the `Provider` class doesn't mark the thread as read anymore
- Method chaining
- Mark messages as deleted (for a single participant / for all)
- When a thread/message is marked as deleted, it's not marked as read anymore.
- Sent message is now automatically marked as read for the sender
- `getThread` method in the `Provider` class doesn't mark the thread as read anymore
- When a thread/message is marked as deleted, it's not marked as read anymore

New getters for Threads and Messages:
- Added optimized version for `getFirst(Last)Message` methods, specifically `getFirst(Last)MessageByThread` and `getFirst(Last)MessageByThreadQueryBuilder` which doesn't load the entire collection
- `getNbUnreadMessageByParticipantAndThread`
- `getNbSentMessageByParticipantAndThread`
- `getMessageByThreadQueryBuilder`
- `getThreadsCreatedByParticipantQueryBuilder`
- `getParticipantThreadsQueryBuilder`
- `getUnreadMessageByParticipantQueryBuilder`
- `getUnreadMessageByParticipantAndThreadQueryBuilder`
- `getNbParticipantThreadsQueryBuilder`