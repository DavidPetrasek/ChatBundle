Configuration Reference
=======================

All configuration options are listed below:

```yaml
# app/config/config.yml

fos_chat:
    db_driver:              orm
    thread_class:           App\Entity\Thread
    message_class:          App\Entity\Message
    participant_provider:   fos_chat.participant_provider    # See Security\ParticipantProviderInterface
    authorizer:             fos_chat.authorizer              # See Security\AuthorizerInterface
    spam_detector:          fos_chat.akismet_spam_detector   # See SpamDetection\SpamDetectorInterface
```
