<?php
declare(strict_types=1);

namespace Djdmg\TransparentPixelBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class TransparentPixelExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Rien d'obligatoire ici, services.yaml couvre l'autowire/autoconfigure
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('doctrine')) {
            return;
        }

        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => [
                    'TransparentPixelBundle' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => \dirname(__DIR__) . '/Entity',
                        'prefix' => 'Djdmg\\TransparentPixelBundle\\Entity',
                        'alias' => 'TP',
                    ],
                ],
            ],
        ]);
    }
}