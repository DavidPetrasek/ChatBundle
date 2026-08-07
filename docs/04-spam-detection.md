[Back to index](00-index.md)

Spam detection
==============

Akismet
-------------

Install:

```bash
composer req omines/akismet
```

Then configure the service:

```yaml
# config/packages/fos_chat.yaml

fos_chat:
    spam_detector: akismet
```

Finally, in your `.env`:

```env
AKISMET_API_KEY="123456"
AKISMET_SITE_URL="https://my.site.com
```
Note: If `AKISMET_SITE_URL` is not specified, it is obtained automatically.

Example usage:
```php
    use FOS\ChatBundle\Service\SpamDetection\AkismetSpamDetector;
    ...
    $spamStatus = $akismetSpamDetector->check($message->getBody());      

    match ($spamStatus) 
    {
        SpamStatus::DISCARD => throw new AccessDeniedException('Severe spam detected.'),
        SpamStatus::SPAM => $this->holdForModeration($message),
        SpamStatus::HAM => $this->publishMessage($message),
    };
```

Custom implementation
----------------

Let's say you create `App\Service\MySpamDetector` service, provided the
class implements `FOS\ChatBundle\Service\SpamDetection\SpamDetectorInterface`.

Then configure the service:

```yaml
# config/packages/fos_chat.yaml

fos_chat:
    spam_detector: App\Service\MySpamDetector
```
