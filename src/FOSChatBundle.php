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
                ->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('thread_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('message_class')->isRequired()->cannotBeEmpty()->end()
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
    }
}
