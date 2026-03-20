<?php

declare (strict_types=1);
namespace Bab_Dev\Pagerfanta_Bundle\Dependency_Injection\Compiler_Pass;

use Bab_Dev\Pagerfanta_Bundle\View\Container_Backed_Immutable_View_Factory;
use Symfony\Component\Dependency_Injection\Compiler\Compiler_Pass_Interface;
use Symfony\Component\Dependency_Injection\Compiler\Service_Locator_Tag_Pass;
use Symfony\Component\Dependency_Injection\Container_Builder;
use Symfony\Component\Dependency_Injection\Reference;
/**
 * @internal
 */
final class Register_Pagerfanta_Views_Pass implements Compiler_Pass_Interface
{
    public function process(Container_Builder $container): void
    {
        if (!$container->has_definition('pagerfanta.view_factory')) {
            return;
        }
        $definition = $container->get_definition('pagerfanta.view_factory');
        if (Container_Backed_Immutable_View_Factory::class === $definition->get_class()) {
            /** @var array<string, Reference> $locator */
            $locator = [];
            /** @var array<string, string> $serviceMap */
            $service_map = [];
            foreach ($container->find_tagged_service_ids('pagerfanta.view') as $service_id => $arguments) {
                $alias = $arguments[0]['alias'] ?? $service_id;
                $locator[$alias] = new Reference($service_id);
                $service_map[$alias] = $service_id;
            }
            $definition->replace_argument(0, Service_Locator_Tag_Pass::register($container, $locator));
            $definition->replace_argument(1, $service_map);
            return;
        }
        foreach ($container->find_tagged_service_ids('pagerfanta.view') as $service_id => $arguments) {
            $alias = $arguments[0]['alias'] ?? $service_id;
            $definition->add_method_call('set', [$alias, new Reference($service_id)]);
        }
    }
}