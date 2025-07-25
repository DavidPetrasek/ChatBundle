<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\ChatBundle\Command\ConfigureCommand;
use FOS\ChatBundle\Maker\Entities;
use FOS\ChatBundle\Service\Composer\Composer;
use FOS\ChatBundle\Service\Provider\Provider;
use FOS\ChatBundle\Service\Reader\Reader;
use FOS\ChatBundle\Security\Authorizer;
use FOS\ChatBundle\Service\Sender\Sender;


return function(ContainerConfigurator $container): void 
{
    $container->services()

         ->set(ConfigureCommand::class)
            ->args([
                param('kernel.project_dir'),
            ])
            ->tag('console.command')

        ->set(Entities::class)
            ->tag('maker.command')


        ->set('fos_chat.provider', Provider::class)
            ->args([
                service('fos_chat.thread_manager'),
                service('fos_chat.message_manager'),
                service('fos_chat.authorizer'),
                service('fos_chat.participant_provider'),
            ])
            ->alias(Provider::class, 'fos_chat.provider')

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

        ->set('fos_chat.thread_reader', Reader::class)
        ->args([
            service('fos_chat.participant_provider'),
            service('fos_chat.thread_manager'),
            service('event_dispatcher')
        ])
        ->alias(Reader::class, 'fos_chat.thread_reader')

        ->set('fos_chat.message_reader', Reader::class)
        ->args([
            service('fos_chat.participant_provider'),
            service('fos_chat.message_manager'),
            service('event_dispatcher')
        ])
        ->alias(Reader::class, 'fos_chat.message_reader')

        ->set('fos_chat.authorizer', Authorizer::class)
        ->args([
            service('fos_chat.participant_provider')
        ])
    ;
}; 