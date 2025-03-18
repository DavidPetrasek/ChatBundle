<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\ChatBundle\Composer\Composer;
use FOS\ChatBundle\Sender\Sender;

return function(ContainerConfigurator $container): void 
{
    $container->services()

        ->set('fos_chat.composer', Composer::class)
            ->args([
                service('fos_chat.message_manager'),
                service('fos_chat.thread_manager'),
            ])
        ->alias(Composer::class, 'fos_chat.composer')

        ->set('fos_chat.sender', Sender::class)
            ->args([
                service('fos_chat.message_manager'),
                service('fos_chat.thread_manager'),
                service('event_dispatcher')
            ])
        ->alias(Sender::class, 'fos_chat.sender')
    ;
};