[Back to index](00-index.md)

Configuration Reference
=======================

All configuration options with default values. Possible values are commented out.

```yaml
# config/packages/fos_chat.yaml

fos_chat:
    # Required
    db_driver:      # orm | mongodb
    thread_class:   # custom FQCN
    message_class:  # custom FQCN

    # Optional
    spam_detector:                                       # akismet | custom FQCN or service id
    participant_provider: fos_chat.participant_provider  # custom FQCN or service id
```