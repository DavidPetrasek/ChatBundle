[Back to index](00-index.md)

Templating Helpers
==================


```html+jinja
{# template.html.twig #}

{# Know if a message is read by the authenticated participant #}
{% if not fos_chat_read(message) %} This message is new! {% endif %}

{# Know if a thread is read by the authenticated participant. Yes, it's the same function. #}
{% if not fos_chat_read(thread) %} This thread is new! {% endif %}

{# Get the number of new threads for the authenticated participant #}
You have {{ fos_chat_nb_unread() }} new messages
```
