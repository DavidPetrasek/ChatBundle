Setting up FOSChatBundle
===========================

### Step 1 - Requirements and Installing the bundle

The first step is to tell composer that you want to download FOSChatBundle which can
be achieved by typing the following at the command prompt:

```bash
composer require friendsofsymfony/chat-bundle
```

### Step 2 - Setting up your user class

FOSChatBundle requires that your user class implement `ParticipantInterface`. This
bundle does not have any direct dependencies to any particular UserBundle or
implementation of a user, except that it must implement the above interface.

Your user class may look something like the following:

```php
<?php
// src/App/Entity/User.php

use Doctrine\ORM\Mapping as ORM;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\UserBundle\Model\User as BaseUser;

#[ORM\Entity]
class User extends BaseUser implements ParticipantInterface
{
}
```

### Step 3 - Set up FOSChatBundle's models

FOSChatBundle has multiple models that must be implemented by you in an application
bundle (that may or may not be a child of FOSChatBundle).

We provide examples for both Mongo DB and ORM.

- [Example entities for Doctrine ORM][]
- [Example documents for Doctrine ODM][]


## Installation Finished

At this point, the bundle has been installed and configured and should be ready for use.
You can continue reading documentation by returning to the [index][].

[Example entities for Doctrine ORM]: 01a-orm-models.md
[Example documents for Doctrine ODM]: 01b-odm-models.md
[index]: 00-index.md
[Using other UserBundles]: 99-using-other-user-bundles.md
