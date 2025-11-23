# Changelog
All notable changes to this project are documented in this file.


## [3.0.0] - 202X-XX-XX
### Added
New features:
- Installer
- Automatic reply
- Participant's status
- Track when something was read or deleted
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
- Add `\Service` to some  use statements (https://github.com/DavidPetrasek/ChatBundle/commit/4604c13993eef7a2901623c159becbba5b0b2de4)
 
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
