<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\ChatBundle\Service\SpamDetection\AkismetSpamDetector;


return function(ContainerConfigurator $container): void 
{
    $container->services()

        ->set('fos_chat.akismet_spam_detector', AkismetSpamDetector::class)
            ->args([                        
                // service(''), //TODO: implement
                service('fos_chat.participant_provider'),
            ])
            ->alias(AkismetSpamDetector::class, 'fos_chat.akismet_spam_detector')
            ->alias('fos_chat.spam_detector', 'fos_chat.akismet_spam_detector')
    ;
}; 