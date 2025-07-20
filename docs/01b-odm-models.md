Concrete classes for Doctrine ODM
=================================

This page lists some example implementations of FOSChatBundle models for the Doctrine
MongoDB ODM.

Given the examples below with their namespaces and class names, you need to configure
FOSChatBundle to tell them about these classes.

Add the following to your `config/packages/fos_chat.yaml` file.

```yaml
# config/packages/fos_chat.yaml

fos_chat:
    db_driver: mongodb
    thread_class: App\Document\Thread
    message_class: App\Document\Message
```

You may have to include the MessageBundle in your Doctrine mapping configuration,
along with the bundle containing your custom Thread and Message classes:

```yaml
# config/packages/fos_chat.yaml

doctrine_mongodb:
    document_managers:
        default:
            mappings:
                FOSChatBundle: ~
```


[Continue with the installation][]

Message class
-------------

```php
<?php
// src/App/Document/Message.php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\ChatBundle\Document\Message as BaseMessage;

/**
 * @MongoDB\Document
 */
class Message extends BaseMessage
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\EmbedMany(targetDocument="App\Document\MessageMetadata")
     */
    protected $metadata;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\Thread")
     */
    protected $thread;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\User")
     */
    protected $sender;
}
```

MessageMetadata class
---------------------

```php
<?php
// src/App/Document/MessageMetadata.php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FOS\ChatBundle\Document\MessageMetadata as BaseMessageMetadata;

/**
 * @ODM\EmbeddedDocument
 */
class MessageMetadata extends BaseMessageMetadata
{
    /**
     * @ODM\ReferenceOne(targetDocument="App\Document\User")
     */
    protected $participant;
}
```

Thread class
------------

```php
<?php
// src/App/Document/Thread.php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\ChatBundle\Document\Thread as BaseThread;

/**
 * @MongoDB\Document
 */
class Thread extends BaseThread
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\ReferenceMany(targetDocument="App\Document\Message")
     */
    protected $messages;

    /**
     * @MongoDB\EmbedMany(targetDocument="App\Document\ThreadMetadata")
     */
    protected $metadata;

    /**
     * @MongoDB\ReferenceMany(targetDocument="App\Document\User")
     */
    protected $participants;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\User")
     */
    protected $createdBy;
}
```

ThreadMetadata class
--------------------

```php
<?php
// src/App/Document/ThreadMetadata.php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FOS\ChatBundle\Document\ThreadMetadata as BaseThreadMetadata;

/**
 * @ODM\EmbeddedDocument
 */
class ThreadMetadata extends BaseThreadMetadata
{
    /**
     * @ODM\ReferenceOne(targetDocument="App\Document\User")
     */
    protected $participant;
}
```

[Continue with the installation]: 01-installation.md
