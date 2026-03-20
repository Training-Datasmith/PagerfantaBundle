<?php

declare (strict_types=1);
namespace Bab_Dev\Pagerfanta_Bundle;

use Bab_Dev\Pagerfanta_Bundle\Dependency_Injection\Bab_Dev_Pagerfanta_Extension;
use Bab_Dev\Pagerfanta_Bundle\Dependency_Injection\Compiler_Pass\Register_Pagerfanta_Views_Pass;
use Bab_Dev\Pagerfanta_Bundle\Dependency_Injection\Compiler_Pass\Register_Twig_Undefined_Callable_Pass;
use Symfony\Component\Dependency_Injection\Container_Builder;
use Symfony\Component\Dependency_Injection\Extension\Extension_Interface;
use Symfony\Component\Http_Kernel\Bundle\Bundle;
final class Bab_Dev_Pagerfanta_Bundle extends Bundle
{
    public function build(Container_Builder $container): void
    {
        parent::build($container);
        $container->add_compiler_pass(new Register_Pagerfanta_Views_Pass());
        $container->add_compiler_pass(new Register_Twig_Undefined_Callable_Pass());
    }
    public function get_container_extension(): ?Extension_Interface
    {
        if (!isset($this->extension)) {
            $this->extension = new Bab_Dev_Pagerfanta_Extension();
        }
        return $this->extension ?: null;
    }
    public function get_path(): string
    {
        return \dirname(__DIR__);
    }
}