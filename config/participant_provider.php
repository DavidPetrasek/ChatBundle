<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\ChatBundle\Security\ParticipantProvider;

return function(ContainerConfigurator $container): void 
{
    $container->services()

        ->set('fos_chat.participant_provider', ParticipantProvider::class)
        ->args([
            service('security.helper')
        ])
    ;
}; 