<?php

namespace FOS\ChatBundle\Tests\Functional;

use FOS\ChatBundle\FOSChatBundle;
use FOS\ChatBundle\Tests\Functional\Entity\Message;
use FOS\ChatBundle\Tests\Functional\Entity\Thread;
use FOS\ChatBundle\Tests\Functional\Entity\UserProvider;
use FOS\ChatBundle\Tests\Functional\EntityManager\MessageManager;
use FOS\ChatBundle\Tests\Functional\EntityManager\ThreadManager;
use FOS\ChatBundle\Tests\Functional\Form\UserToUsernameTransformer;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * @author Guilhem N. <guilhem.niot@gmail.com>
 */
class TestKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new FOSChatBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    private function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', [
            'secret' => 'MySecretKey',
            'test' => null,
            'form' => null,
            'templating' => [
                'engines' => ['twig'],
            ],
        ]);

        $c->loadFromExtension('security', [
            'providers' => ['permissive' => ['id' => 'app.user_provider']],
            'encoders' => [\FOS\ChatBundle\Tests\Functional\Entity\User::class => 'plaintext'],
            'firewalls' => ['main' => ['http_basic' => true]],
        ]);

        $c->loadFromExtension('twig', [
            'strict_variables' => '%kernel.debug%',
        ]);

        $c->loadFromExtension('fos_chat', [
            'db_driver' => 'orm',
            'thread_class' => Thread::class,
            'message_class' => Message::class,
        ]);

        $c->register('fos_user.user_to_username_transformer', UserToUsernameTransformer::class);
        $c->register('app.user_provider', UserProvider::class);
        $c->addCompilerPass(new RegisteringManagersPass());
    }
}

class RegisteringManagersPass implements CompilerPassInterface {
    public function process(ContainerBuilder $container): void
    {
        $container->register('fos_chat.message_manager.default', MessageManager::class);
        $container->register('fos_chat.thread_manager.default', ThreadManager::class);
    }
}
