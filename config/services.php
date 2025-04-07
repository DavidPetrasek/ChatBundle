<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\ChatBundle\Composer\Composer;
use FOS\ChatBundle\Provider\Provider;
use FOS\ChatBundle\Reader\Reader;
use FOS\ChatBundle\Security\Authorizer;
use FOS\ChatBundle\Security\ParticipantProvider;
use FOS\ChatBundle\Sender\Sender;


return function(ContainerConfigurator $container): void 
{
    $container->services()

        ->set('fos_chat.provider', Provider::class)
            ->args([
                service('fos_chat.thread_manager'),
                service('fos_chat.message_manager'),
                service('fos_chat.thread_reader'),
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
            // ->alias(Reader::class, 'fos_chat.thread_reader')

        ->set('fos_chat.message_reader', Reader::class)
        ->args([
            service('fos_chat.participant_provider'),
            service('fos_chat.message_manager'),
            service('event_dispatcher')
        ])
            // ->alias(Reader::class, 'fos_chat.message_reader')

        ->set('fos_chat.authorizer', Authorizer::class)
        ->args([
            service('fos_chat.participant_provider')
        ])

        ->set('fos_chat.participant_provider', ParticipantProvider::class)
        ->args([
            service('security.helper')
        ])
    ;
}; 