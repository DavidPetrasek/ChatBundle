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
- You might need to fork this repo before and then swap the url below
1) Add the following to your composer.json:
```json
"repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:DavidPetrasek/ChatBundle.git"
        }
    ]
```
2) Run: `composer req davidpetrasek/chat-bundle @dev`