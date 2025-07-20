Concrete classes for Doctrine ORM
=================================

This page lists some example implementations of FOSChatBundle models for the Doctrine
ORM.

Given the examples below with their namespaces and class names, you need to configure
FOSChatBundle to tell them about these classes.

Add the following to your `config/packages/fos_chat.yaml` file.

```yaml
# config/packages/fos_chat.yaml

fos_chat:
    db_driver: orm
    thread_class: App\Entity\Thread
    message_class: App\Entity\Message
```

[Continue with the installation][]

Message class
-------------

```php
<?php
// src/App/Entity/Message.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\ChatBundle\Entity\Message as BaseMessage;

#[ORM\Entity]
class Message extends BaseMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options:["unsigned" => true])]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Thread::class, inversedBy: 'messages')]
    protected \FOS\ChatBundle\Model\ThreadInterface $thread;

    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class)]
    protected \FOS\ChatBundle\Model\ParticipantInterface $sender;

    /**
     * @var Collection<int, \App\Entity\MessageMetadata>
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\MessageMetadata::class, mappedBy: 'message', cascade: ['all'])]
    protected Collection $metadata;
    
    public function __construct()
    {
        parent::__construct();
        $this->metadata = new ArrayCollection();
    }
}
```

MessageMetadata class
---------------------

```php
<?php
// src/App/Entity/MessageMetadata.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\ChatBundle\Entity\MessageMetadata as BaseMessageMetadata;

#[ORM\Entity]
class MessageMetadata extends BaseMessageMetadata
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options:["unsigned" => true])]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Message, inversedBy: 'metadata')]
    protected \FOS\ChatBundle\Model\MessageInterface $message;

    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class)]
    protected \FOS\ChatBundle\Model\ParticipantInterface $participant;
}
```

Thread class
------------

```php
<?php
// src/App/Entity/Thread.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\ChatBundle\Entity\Thread as BaseThread;

#[ORM\Entity]
class Thread extends BaseThread
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options:["unsigned" => true])]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class)]
    protected \FOS\ChatBundle\Model\ParticipantInterface $createdBy;

    /**
     * @var Collection<int, \App\Entity\Message::class>
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Message::class, mappedBy: 'thread')]
    protected Collection $messages;

    /**
     * @var Collection<int, \App\Entity\ThreadMetadata>
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\ThreadMetadata::class, mappedBy: 'thread', cascade: ['all'])]
    protected Collection $metadata;
    
    public function __construct()
    {
        parent::__construct();
        $this->messages = new ArrayCollection();
        $this->metadata = new ArrayCollection();
    }
}
```

ThreadMetadata class
--------------------

```php
<?php
// src/App/Entity/ThreadMetadata.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\ChatBundle\Entity\ThreadMetadata as BaseThreadMetadata;

#[ORM\Entity]
class ThreadMetadata extends BaseThreadMetadata
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options:["unsigned" => true])]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Thread::class, inversedBy: 'metadata')]
    protected \FOS\ChatBundle\Model\ThreadInterface $thread;

    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class)]
    protected \FOS\ChatBundle\Model\ParticipantInterface $participant;
}
```

[Continue with the installation]: 01-installation.md
