Configuration Reference
=======================

All configuration options are listed below::

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
    user_transformer:       fos_user.user_transformer           # See Symfony\Component\Form\DataTransformerInterface
    search:
        finder:             fos_chat.search_finder           # See Finder\FinderInterface
        query_factory:      fos_chat.search_query_factory    # See Finder\QueryFactoryInterface
        query_parameter:    'q'                                     # Request query parameter containing the term
    new_thread_form:
        factory:            fos_chat.new_thread_form.factory # See FormFactory\NewThreadMessageFormFactory
        type:               FOS\ChatBundle\FormType\NewThreadMessageFormType
        handler:            fos_chat.new_thread_form.handler # See FormHandler\NewThreadMessageFormHandler
        name:               message
    reply_form:
        factory:            fos_chat.reply_form.factory      # See FormFactory\ReplyMessageFormFactory
        type:               FOS\ChatBundle\FormType\ReplyMessageFormType
        handler:            fos_chat.reply_form.handler      # See FormHandler\ReplyMessageFormHandler
        name:               message
```
