<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\ChatBundle\EntityManager\MessageManager;
use FOS\ChatBundle\EntityManager\ThreadManager;

return function(ContainerConfigurator $container): void 
{
    $container->services()

        ->set('fos_chat.message_manager', MessageManager::class)
            ->args([
                service('doctrine.orm.entity_manager'),
                param('fos_chat.message_class'),
                param('fos_chat.message_meta_class'),
            ])
        
        ->set('fos_chat.thread_manager', ThreadManager::class)
            ->args([
                service('doctrine.orm.entity_manager'),
                param('fos_chat.thread_class'),
                param('fos_chat.thread_meta_class'),
                service('fos_chat.message_manager'),
            ])
    ;
};