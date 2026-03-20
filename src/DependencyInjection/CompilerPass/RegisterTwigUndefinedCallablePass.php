<?php

declare (strict_types=1);
namespace Bab_Dev\Pagerfanta_Bundle\Dependency_Injection\Compiler_Pass;

use Symfony\Component\Dependency_Injection\Compiler\Compiler_Pass_Interface;
use Symfony\Component\Dependency_Injection\Container_Builder;
use Symfony\Component\Dependency_Injection\Reference;
/**
 * @internal
 */
final class Register_Twig_Undefined_Callable_Pass implements Compiler_Pass_Interface
{
    public function process(Container_Builder $container): void
    {
        if (!$container->has_definition('twig')) {
            return;
        }
        $container->get_definition('twig')->add_method_call('registerUndefinedFunctionCallback', [[new Reference('pagerfanta.undefined_callable_handler'), 'onUndefinedFunction']]);
    }
}