Configuring multiple recipients support
=======================================

Configure your application

```yaml
# app/config/config.yml

fos_chat:
    db_driver: orm
    thread_class: App\Entity\Thread
    message_class: App\Entity\Message
    new_thread_form:
      type:               FOS\ChatBundle\FormType\NewThreadMultipleMessageFormType
      handler:            fos_chat.new_thread_multiple_form.handler
      model:              FOS\ChatBundle\FormModel\NewThreadMultipleMessage
      name:               message
```

Currently multiple functionality is based on FOSUserBundle but you can define custom form type for
multiple recipients and use your own recipients transformer.
