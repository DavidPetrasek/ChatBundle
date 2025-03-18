<?php

namespace FOS\ChatBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class defines the configuration information for the bundle.
 */
class Configuration
{
    const ROOT_NAME = 'fos_chat';

    /**
     * Generates the configuration tree.
     */
    public function getConfigTreeBuilder()
    {
        return;

        $treeBuilder = new TreeBuilder(self::ROOT_NAME);
        if (\method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root(self::ROOT_NAME);
        }

        $rootNode
            ->children()
                
                ->scalarNode('message_manager')->defaultValue('fos_chat.message_manager.default')->cannotBeEmpty()->end()
                ->scalarNode('thread_manager')->defaultValue('fos_chat.thread_manager.default')->cannotBeEmpty()->end()
                ->scalarNode('sender')->defaultValue('fos_chat.sender.default')->cannotBeEmpty()->end()
                ->scalarNode('composer')->defaultValue('fos_chat.composer.default')->cannotBeEmpty()->end()
                ->scalarNode('provider')->defaultValue('fos_chat.provider.default')->cannotBeEmpty()->end()
                ->scalarNode('participant_provider')->defaultValue('fos_chat.participant_provider.default')->cannotBeEmpty()->end()
                ->scalarNode('authorizer')->defaultValue('fos_chat.authorizer.default')->cannotBeEmpty()->end()
                ->scalarNode('message_reader')->defaultValue('fos_chat.message_reader.default')->cannotBeEmpty()->end()
                ->scalarNode('thread_reader')->defaultValue('fos_chat.thread_reader.default')->cannotBeEmpty()->end()
                ->scalarNode('deleter')->defaultValue('fos_chat.deleter.default')->cannotBeEmpty()->end()
                ->scalarNode('spam_detector')->defaultValue('fos_chat.noop_spam_detector')->cannotBeEmpty()->end()
                ->scalarNode('twig_extension')->defaultValue('fos_chat.twig_extension.default')->cannotBeEmpty()->end()
                ->scalarNode('user_transformer')->defaultValue('fos_user.user_to_username_transformer')->cannotBeEmpty()->end()
                ->arrayNode('search')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('query_factory')->defaultValue('fos_chat.search_query_factory.default')->cannotBeEmpty()->end()
                        ->scalarNode('finder')->defaultValue('fos_chat.search_finder.default')->cannotBeEmpty()->end()
                        ->scalarNode('query_parameter')->defaultValue('q')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('new_thread_form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('factory')->defaultValue('fos_chat.new_thread_form.factory.default')->cannotBeEmpty()->end()
                        ->scalarNode('type')->defaultValue(\FOS\ChatBundle\FormType\NewThreadMessageFormType::class)->cannotBeEmpty()->end()
                        ->scalarNode('handler')->defaultValue('fos_chat.new_thread_form.handler.default')->cannotBeEmpty()->end()
                        ->scalarNode('name')->defaultValue('message')->cannotBeEmpty()->end()
                        ->scalarNode('model')->defaultValue(\FOS\ChatBundle\FormModel\NewThreadMessage::class)->end()
                    ->end()
                ->end()
                ->arrayNode('reply_form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('factory')->defaultValue('fos_chat.reply_form.factory.default')->cannotBeEmpty()->end()
                        ->scalarNode('type')->defaultValue(\FOS\ChatBundle\FormType\ReplyMessageFormType::class)->cannotBeEmpty()->end()
                        ->scalarNode('handler')->defaultValue('fos_chat.reply_form.handler.default')->cannotBeEmpty()->end()
                        ->scalarNode('name')->defaultValue('message')->cannotBeEmpty()->end()
                        ->scalarNode('model')->defaultValue(\FOS\ChatBundle\FormModel\ReplyMessage::class)->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
