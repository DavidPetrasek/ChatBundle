Messaging permissions
======================

The default permissions authorizer service will authenticate a user if they're a
participant of the thread and is very permissive by default.

You can implement your own permissions service to replace the built in service and tell
FOSChatBundle about it:

```yaml
# config/packages/fos_chat.yaml

fos_chat:
    authorizer: app.authorizer
```

Any such service must implement `FOS\ChatBundle\Security\AuthorizerInterface`.

[Return to the documentation index](00-index.md)
