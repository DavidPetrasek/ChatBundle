# Changelog
All notable changes to this project are documented in this file.


## [3.0.0] - 202X-XX-XX
### Added
New features:
- Installer
- Automatic reply (whether the message was sent by the system)
- Participant's status
- Record datetime when read or deleted
- Method chaining
- Mark messages as deleted (for a single participant / for all)

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

### Changed
- Switch DateTime to DateTimeImmutable
- Add `\Service` to these use statements (these are already corrected):
`use FOS\ChatBundle\Service\Composer\Composer;`

`use FOS\ChatBundle\Service\Deleter\Deleter;`

`use FOS\ChatBundle\Service\DocumentManager\MessageManager;`

`use FOS\ChatBundle\Service\DocumentManager\ThreadManager;`

`use FOS\ChatBundle\Service\EntityManager\MessageManager;`

`use FOS\ChatBundle\Service\EntityManager\ThreadManager;`

`use FOS\ChatBundle\Service\Provider\Provider;`

`use FOS\ChatBundle\Service\Reader\Reader;`

`use FOS\ChatBundle\Service\Sender\Sender;`

`use FOS\ChatBundle\Service\SpamDetection\SpamDetectorInterface;`

 
Rename:
- `getIsRead` -> `isRead`
- `getIsDeleted` -> `isDeleted`
- `getIsSpam` -> `isSpam`
- `setIsRead` -> `setRead`
- `setIsDeleted` -> `setDeleted`
- `setIsSpam` -> `setSpam`

Logic:
- When a thread/message is marked as deleted, it's not marked as read anymore.
- Sent message is now automatically marked as read for the sender
- `getThread` method in the `Provider` class doesn't mark the thread as read anymore
- When a thread/message is marked as deleted, it's not marked as read anymore

### Fixed
