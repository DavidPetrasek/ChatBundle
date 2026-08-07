[Back to index](00-index.md)

Sending a message programatically
=================================

Composing a message
-------------------

The service container contains a service to compose messages and one to send them.
This is probably all you will need in many cases.

To compose a message we retrieve the composer service `FOS\ChatBundle\Service\Composer\Composer` and compose our message:

```php
$threadBuilder = $composer->newThread();
$threadBuilder
    ->addRecipient($recipient) // Retrieved from your backend, your user manager or ...
    ->setSender($security->getUser())
    ->setSubject('Stof commented on your pull request #456789')
    ->setBody('You have a typo, : mondo instead of mongo. Also for coding standards ...');
```

Sending a message
-----------------
Inject `FOS\ChatBundle\Service\Sender\Sender` into your service or controller.

Now all you have to do to send your message is get the sender and tell it to send

```php
$sender->send($threadBuilder->getMessage());
```

That's it, your message should now have been sent
