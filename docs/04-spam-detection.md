Spam detection
==============

Using Akismet
-------------

Install AkismetBundle (https://github.com/ornicar/OrnicarAkismetBundle).

Then, set the spam detector service accordingly:

```yaml
# app/config/config.yml

fos_chat:
    spam_detector: fos_chat.akismet_spam_detector
```

Other strategies
----------------

You can use any spam detector service, including one of your own, provided the
class implements ``FOS\ChatBundle\SpamDetection\SpamDetectorInterface``.

[Return to the documentation index](00-index.md)
