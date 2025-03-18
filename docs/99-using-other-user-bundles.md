Using other UserBundles
=======================

By default, FOSChatBundle depends on the UserToUsername data transformer provided by FOSUserBundle.
However, if you do not use FOSUserBundle, it is possible to implement your own version of this
transformer and tell to FOSChatBundle to use it.

> **Note**: For many cases, just implementing your own UserToUsername transformer will be enough, but
> depending on how your users system works you may need to change other things.

The transformer is just a service that know how to transform usernames into User objects and vice-versa.
You can base your own on this one:

``` php
<?php
// src/App/Form/DataTransformer/UserToUsernameTransformer.php

namespace App\Form\DataTransformer;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

use App\Entity\User;

/**
 * Transforms between a User instance and a username string
 */
class UserToUsernameTransformer implements DataTransformerInterface
{
    private EntityRepository $repository;

    public function __construct(Registry $doctrine)
    {
        $this->repository = $doctrine->getManager()->getRepository('App:User');
    }

    /**
     * Transforms a User instance into a username string.
     * @throws UnexpectedTypeException if the given value is not a User instance
     */
    public function transform(User|null $value) : string|null
    {
        if (null === $value) {
            return null;
        }

        if (! $value instanceof User) {
            throw new UnexpectedTypeException($value, 'App\Entity\User');
        }

        return $value->getUsername();
    }

    /**
     * Transforms a username string into a User instance.
     * @throws UnexpectedTypeException if the given value is not a string
     */
    public function reverseTransform(string $value) : User
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (! is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        return $this->repository->findOneByUsername($value);
    }
}
```

Once your transformer created, you still have to tell FOSChatBundle to use it.
For the moment, there is no configuration key to do it so we will emulate the
FOSUserBundle transformer by using its name as an alias of our own service:

``` xml
<!-- app/config/services.xml -->

<service id="app.user_to_username_transformer" class="App\Form\DataTransformer\UserToUsernameTransformer">
    <argument type="service" id="doctrine" />
</service>

<service id="fos_user.user_to_username_transformer" alias="app.user_to_username_transformer" />
```

### Problems you may encounter

#### User identifier field is not `id`

If the identifier in your User entity is not named `id` (Drupal named it `uid` for instance),
you have to define your own `thread_manager` and `message_manager` to change the requests
made by the bundle.

You can copy the default ones (in `FOS\ChatBundle\EntityManager` if you use the Doctrine ORM)
into your bundle, change their queries and register them as services:

``` xml
<!-- app/config/services.xml -->

<service id="app.message_manager" class="App\EntityManager\MessageManager" public="false">
    <argument type="service" id="doctrine.orm.entity_manager" />
    <argument>%fos_chat.message_class%</argument>
    <argument>%fos_chat.message_meta_class%</argument>
</service>

<service id="app.thread_manager" class="App\EntityManager\ThreadManager" public="false">
    <argument type="service" id="doctrine.orm.entity_manager" />
    <argument>%fos_chat.thread_class%</argument>
    <argument>%fos_chat.thread_meta_class%</argument>
    <argument type="service" id="app.message_manager" />
</service>
```

Once done, tell FOSChatBundle to use them in the configuration:

``` yaml
# app/config/config.yml

fos_chat:
	# ...
    thread_manager: app.thread_manager
    message_manager: app.message_manager
```

#### The default form does not work with my User entity

You have to redefine two things :
  - the form type `FOS\ChatBundle\FormType\NewThreadMessageFormType`
  - the form factory `FOS\ChatBundle\FormType\NewThreadMessageFormType`

You can copy and paste the bundle versions into your application and define them as services:

``` xml
<service id="app.new_thread_form_type" class="App\Form\NewThreadMessageFormType" public="false">
    <argument type="service" id="app.user_to_username_transformer" />
</service>

<service id="app.new_thread_form_factory" class="App\Form\NewThreadMessageFormFactory" public="false">
    <argument type="service" id="form.factory" />
    <argument type="service" id="fos_chat.new_thread_form.type" />
    <argument>%fos_chat.new_thread_form.name%</argument>
    <argument>%fos_chat.new_thread_form.model%</argument>
    <argument type="service" id="doctrine" />
    <argument type="service" id="request" />
</service>
```

And configure the bundle to use your services:

``` yaml
# app/config/config.yml

fos_chat:
    # ...
    new_thread_form:
        type: app.new_thread_form_type
        factory: app.new_thread_form_factory
```

#### Another problem?

If you have another problem or if this documentation is not clear enough for you to implement your own user system with FOSChatBundle, don't hesitate to create an issue in the [Github tracker](https://github.com/FriendsOfSymfony/FOSChatBundle/issues).
