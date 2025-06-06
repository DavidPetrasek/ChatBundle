Configuration Reference
=======================

All configuration options are listed below:

```yaml
# app/config/config.yml

fos_chat:
    db_driver:              orm
    thread_class:           App\Entity\Thread
    message_class:          App\Entity\Message
    message_manager:        fos_chat.message_manager         # See ModelManager\MessageManagerInterface
    thread_manager:         fos_chat.thread_manager          # See ModelManager\ThreadManagerInterface
    sender:                 fos_chat.sender                  # See Sender\SenderInterface
    composer:               fos_chat.composer                # See Composer\ComposerInterface
    provider:               fos_chat.provider                # See Provider\ProviderInterface
    participant_provider:   fos_chat.participant_provider    # See Security\ParticipantProviderInterface
    authorizer:             fos_chat.authorizer              # See Security\AuthorizerInterface
    message_reader:         fos_chat.message_reader          # See Reader\ReaderInterface
    thread_reader:          fos_chat.thread_reader           # See Reader\ReaderInterface
    deleter:                fos_chat.deleter                 # See Deleter\DeleterInterface
    spam_detector:          fos_chat.noop_spam_detector      # See SpamDetection\SpamDetectorInterface
    twig_extension:         fos_chat.twig_extension          # See Twig\Extension\MessageExtension
```
