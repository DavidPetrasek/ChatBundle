<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\ChatBundle\Service\SpamDetection\NoopSpamDetector;

return function(ContainerConfigurator $container): void 
{
    $container->services()

        ->set('fos_chat.noop_spam_detector', NoopSpamDetector::class)
            ->alias(NoopSpamDetector::class, 'fos_chat.noop_spam_detector')
            ->alias('fos_chat.spam_detector', 'fos_chat.noop_spam_detector')
    ;
}; 