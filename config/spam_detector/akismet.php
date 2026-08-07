<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\ChatBundle\Service\SpamDetection\AkismetSpamDetector;

return function(ContainerConfigurator $container): void 
{
    $container->services()

        ->set('fos_chat.akismet_spam_detector', AkismetSpamDetector::class)
            ->args([                        
                env('AKISMET_API_KEY'),
                env('AKISMET_SITE_URL')->default(''),
                service('http_client'),
                service('request_stack'),
                service('fos_chat.participant_provider'),
            ])
            ->alias(AkismetSpamDetector::class, 'fos_chat.akismet_spam_detector')
            ->alias('fos_chat.spam_detector', 'fos_chat.akismet_spam_detector')
    ;
};