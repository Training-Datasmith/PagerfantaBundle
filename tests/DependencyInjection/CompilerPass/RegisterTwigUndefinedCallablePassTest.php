<?php

declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\DependencyInjection\CompilerPass;

use BabDev\PagerfantaBundle\DependencyInjection\CompilerPass\RegisterTwigUndefinedCallablePass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Twig\Environment;

final class RegisterTwigUndefinedCallablePassTest extends AbstractCompilerPassTestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!class_exists(Environment::class)) {
            self::markTestSkipped('Test requires Twig');
        }
    }

    public function testRegistersUndefinedCallableWhenTwigServiceIsAvailable(): void
    {
        $this->registerService('twig', Environment::class);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'twig',
            'registerUndefinedFunctionCallback',
            [[new Reference('pagerfanta.undefined_callable_handler'), 'onUndefinedFunction']]
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterTwigUndefinedCallablePass());
    }
}
