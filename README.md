FOSChatBundle
================

This bundle provides chat features for a Symfony application. Features available include:

- Support for both the Doctrine ORM and ODM (not yet) for message storage
- Threaded conversations
- Spam detection support
- Soft deletion of threads and messages
- Permissions for messaging

[![Build Status](https://travis-ci.org/FriendsOfSymfony/FOSChatBundle.png?branch=master)](https://travis-ci.org/FriendsOfSymfony/FOSChatBundle) [![Total Downloads](https://poser.pugx.org/FriendsOfSymfony/chat-bundle/downloads.png)](https://packagist.org/packages/FriendsOfSymfony/chat-bundle) [![Latest Stable Version](https://poser.pugx.org/FriendsOfSymfony/chat-bundle/v/stable.png)](https://packagist.org/packages/FriendsOfSymfony/chat-bundle)

Documentation
-------------

https://github.com/DavidPetrasek/ChatBundle/blob/main/docs/00-index.md



## How to test

1. Clone/download this repo

2. Add to your `composer.json`:
```json
"repositories": [
    {
        "type": "path",
        "url": "/abs/path/to/cloned/repo/ChatBundle",
        "options": 
        {
            "symlink": true
        }
    }
]
```

3. Run: `composer req davidpetrasek/chat-bundle @dev`