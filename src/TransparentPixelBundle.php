<?php
declare(strict_types=1);

namespace Djdmg\TransparentPixelBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;


final class TransparentPixelBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        if (class_exists(DoctrineOrmMappingsPass::class)) {
            $mappings = [
                __DIR__.'/Entity' => 'Djdmg\\TransparentPixelBundle\\Entity',
            ];
            $container->addCompilerPass(
                DoctrineOrmMappingsPass::createAttributeMappingDriver($mappings)
            );
        }
    }
}