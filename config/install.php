<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\ChatBundle\Command\InstallCommand;
use FOS\ChatBundle\Maker\Documents;
use FOS\ChatBundle\Maker\Entities;

return function(ContainerConfigurator $container): void 
{
    $container->services()

         ->set(InstallCommand::class)
            ->args([
                param('kernel.project_dir'),
            ])
            ->tag('console.command')

        ->set(Entities::class)
            ->tag('maker.command')
        ->set(Documents::class)
            ->tag('maker.command')
    ;
}; 