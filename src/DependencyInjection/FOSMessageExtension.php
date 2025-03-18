<?php

namespace FOS\ChatBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FOSMessageExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        return;

        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
       

        $container->setParameter('fos_chat.new_thread_form.model', $config['new_thread_form']['model']);
        $container->setParameter('fos_chat.new_thread_form.name', $config['new_thread_form']['name']);
        $container->setParameter('fos_chat.reply_form.model', $config['reply_form']['model']);
        $container->setParameter('fos_chat.reply_form.name', $config['reply_form']['name']);

        $container->setAlias('fos_chat.message_manager', new Alias($config['message_manager'], true));
        $container->setAlias('fos_chat.thread_manager', new Alias($config['thread_manager'], true));

        $container->setAlias('fos_chat.sender', new Alias($config['sender'], true));
        $container->setAlias('fos_chat.composer', new Alias($config['composer'], true));
        $container->setAlias('fos_chat.provider', new Alias($config['provider'], true));
        $container->setAlias('fos_chat.participant_provider', new Alias($config['participant_provider'], true));
        $container->setAlias('fos_chat.authorizer', new Alias($config['authorizer'], true));
        $container->setAlias('fos_chat.message_reader', new Alias($config['message_reader'], true));
        $container->setAlias('fos_chat.thread_reader', new Alias($config['thread_reader'], true));
        $container->setAlias('fos_chat.deleter', new Alias($config['deleter'], true));
        $container->setAlias('fos_chat.spam_detector', new Alias($config['spam_detector'], true));
        $container->setAlias('fos_chat.twig_extension', new Alias($config['twig_extension'], true));

        // BC management
        $newThreadFormType = $config['new_thread_form']['type'];
        $replyFormType = $config['reply_form']['type'];

        if (!class_exists($newThreadFormType)) {
            @trigger_error('Using a service reference in configuration key "fos_chat.new_thread_form.type" is deprecated since version 1.2 and will be removed in 2.0. Use the class name of your form type instead.', E_USER_DEPRECATED);

            // Old syntax (service reference)
            $container->setAlias('fos_chat.new_thread_form.type', new Alias($newThreadFormType, true));
        } else {
            // New syntax (class name)
            $container->getDefinition('fos_chat.new_thread_form.factory.default')
                ->replaceArgument(1, $newThreadFormType);
        }

        if (!class_exists($replyFormType)) {
            @trigger_error('Using a service reference in configuration key "fos_chat.reply_form.type" is deprecated since version 1.2 and will be removed in 2.0. Use the class name of your form type instead.', E_USER_DEPRECATED);

            // Old syntax (service reference)
            $container->setAlias('fos_chat.reply_form.type', new Alias($replyFormType, true));
        } else {
            // New syntax (class name)
            $container->getDefinition('fos_chat.reply_form.factory.default')
                ->replaceArgument(1, $replyFormType);
        }

        $container->setAlias('fos_chat.new_thread_form.factory', new Alias($config['new_thread_form']['factory'], true));
        $container->setAlias('fos_chat.new_thread_form.handler', new Alias($config['new_thread_form']['handler'], true));
        $container->setAlias('fos_chat.reply_form.factory', new Alias($config['reply_form']['factory'], true));
        $container->setAlias('fos_chat.reply_form.handler', new Alias($config['reply_form']['handler'], true));

        $container->setAlias('fos_chat.search_query_factory', new Alias($config['search']['query_factory'], true));
        $container->setAlias('fos_chat.search_finder', new Alias($config['search']['finder'], true));
        $container->getDefinition('fos_chat.search_query_factory.default')
            ->replaceArgument(1, $config['search']['query_parameter']);

        $container->getDefinition('fos_chat.recipients_data_transformer')
            ->replaceArgument(0, new Reference($config['user_transformer']));
    }
}
