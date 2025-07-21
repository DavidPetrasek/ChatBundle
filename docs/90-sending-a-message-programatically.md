[Back to index](00-index.md)

Sending a message programatically
=================================

Composing a message
-------------------

The service container contains a service to compose messages and one to send them.
This is probably all you will need in many cases.

To compose a message we retrieve the composer service and compose our message:

```php
/** @var Symfony\Bundle\SecurityBundle\Security $security  */
$sender = $security->getUser();
/** @var FOS\ChatBundle\Service\Composer\Composer $chatComposer  */
$threadBuilder = $chatComposer->newThread();
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
/** @var FOS\ChatBundle\Service\Sender\Sender $sender  */
$sender->send($threadBuilder->getMessage());
```

That's it, your message should now have been sent
