<?php
namespace FOS\ChatBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;


class FOSChatBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                // Required
                ->stringNode('db_driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
                ->stringNode('thread_class')->isRequired()->cannotBeEmpty()->end()
                ->stringNode('message_class')->isRequired()->cannotBeEmpty()->end()
                
                // Optional
                ->stringNode('spam_detector')->defaultNull()->end()
                ->stringNode('participant_provider')->defaultValue('fos_chat.participant_provider')->cannotBeEmpty()->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        if (!in_array(strtolower((string) $config['db_driver']), ['orm', 'mongodb'])) {
            throw new \InvalidArgumentException(sprintf('Invalid db driver "%s".', $config['db_driver']));
        }

        $container->parameters()
            ->set('fos_chat.message_class', $config['message_class'])
            ->set('fos_chat.message_meta_class', $config['message_class'].'Metadata')
            ->set('fos_chat.thread_class', $config['thread_class'])
            ->set('fos_chat.thread_meta_class', $config['thread_class'].'Metadata')
            ;

        $container->import('../config/'.$config['db_driver'].'.php');


        // OPTIONAL
        
        // Register one of default spam detectors if specified
        if (in_array(strtolower((string) $config['spam_detector']), ['akismet'])) 
        {
            $container->import('../config/spam_detector/'.$config['spam_detector'].'.php');
        }
        // or set an alias for a custom registered spam detector service
        else if (!is_null($config['spam_detector']))
        {
            $builder->setAlias('fos_chat.spam_detector', $config['spam_detector']);
        }
        
        // Register default participant provider
        if ($config['participant_provider'] === 'fos_chat.participant_provider')
        {
            $container->import('../config/participant_provider.php');
        }
        else // Or set an alias for a custom registered participant provider service
        {
            $builder->setAlias('fos_chat.participant_provider', $config['participant_provider']);
        }
    }
}
