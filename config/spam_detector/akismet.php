<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\ChatBundle\SpamDetection\AkismetSpamDetector;


return function(ContainerConfigurator $container): void 
{
    $container->services()

        ->set('fos_chat.akismet_spam_detector', AkismetSpamDetector::class)
            ->args([                        
                service('fos_chat.participant_provider'),
                service('fos_chat.ornicar_akismet'),
            ])
            ->alias(AkismetSpamDetector::class, 'fos_chat.akismet_spam_detector')
    ;
}; 