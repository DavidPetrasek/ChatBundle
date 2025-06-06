Sending a message programatically
=================================

Composing a message
-------------------

The service container contains a service to compose messages and one to send them.
This is probably all you will need in many cases.

To compose a message we retrieve the composer service and compose our message:

```php
$sender = $this->get('security.context')->getToken()->getUser();
$threadBuilder = $this->get('fos_chat.composer')->newThread();
$threadBuilder
    ->addRecipient($recipient) // Retrieved from your backend, your user manager or ...
    ->setSender($sender)
    ->setSubject('Stof commented on your pull request #456789')
    ->setBody('You have a typo, : mondo instead of mongo. Also for coding standards ...');
```

Sending a message
-----------------

Now all you have to do to send your message is get the sender and tell it to send

```php
$sender = $this->get('fos_chat.sender');
$sender->send($threadBuilder->getMessage());
```

That's it, your message should now have been sent
