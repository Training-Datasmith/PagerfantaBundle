<?php

declare (strict_types=1);
namespace Bab_Dev\Pagerfanta_Bundle\Dependency_Injection;

use Bab_Dev\Pagerfanta_Bundle\Event_Listener\Convert_Not_Valid_Current_Page_To_Not_Found_Listener;
use Bab_Dev\Pagerfanta_Bundle\Event_Listener\Convert_Not_Valid_Max_Per_Page_To_Not_Found_Listener;
use Bab_Dev\Pagerfanta_Bundle\Serializer\Normalizer\Legacy_Pagerfanta_Normalizer;
use Composer\Installed_Versions;
use Pagerfanta\Twig\Extension\Pagerfanta_Extension;
use Symfony\Component\Config\File_Locator;
use Symfony\Component\Dependency_Injection\Container_Builder;
use Symfony\Component\Dependency_Injection\Extension\Prepend_Extension_Interface;
use Symfony\Component\Dependency_Injection\Loader\Php_File_Loader;
use Symfony\Component\Dependency_Injection\Reference;
use Symfony\Component\Http_Kernel\Bundle\Bundle_Interface;
use Symfony\Component\Http_Kernel\Dependency_Injection\Configurable_Extension;
use Symfony\Component\Http_Kernel\Kernel_Events;
use Symfony\Component\Serializer\Normalizer\Normalizer_Interface;
final class Bab_Dev_Pagerfanta_Extension extends Configurable_Extension implements Prepend_Extension_Interface
{
    public function get_alias(): string
    {
        return 'babdev_pagerfanta';
    }
    protected function load_internal(array $merged_config, Container_Builder $container): void
    {
        $loader = new Php_File_Loader($container, new File_Locator(__DIR__ . '/../../config'));
        $loader->load('pagerfanta.php');
        /** @var array<string, class-string<BundleInterface>> $bundles */
        $bundles = $container->get_parameter('kernel.bundles');
        if (isset($bundles['TwigBundle'])) {
            $loader->load('twig.php');
            if (Container_Builder::will_be_available('pagerfanta/twig', Pagerfanta_Extension::class, ['babdev/pagerfanta-bundle'])) {
                $container->get_definition('pagerfanta.twig_runtime')->replace_argument(0, $merged_config['default_view']);
                $container->get_definition('pagerfanta.view.twig')->replace_argument(1, $merged_config['default_twig_template']);
            } else {
                $container->remove_definition('pagerfanta.twig_extension');
                $container->remove_definition('pagerfanta.twig_runtime');
                $container->remove_definition('pagerfanta.view.twig');
            }
        }
        if (isset($bundles['JMSSerializerBundle'])) {
            $loader->load('jms_serializer.php');
        }
        if (interface_exists(Normalizer_Interface::class)) {
            $loader->load('serializer.php');
            if (class_exists(Installed_Versions::class)) {
                $version = Installed_Versions::get_version('symfony/serializer');
                if (null !== $version && version_compare($version, '6.3', '<')) {
                    $container->register('pagerfanta.serializer.normalizer.legacy', Legacy_Pagerfanta_Normalizer::class)->set_decorated_service('pagerfanta.serializer.normalizer')->add_argument(new Reference('.inner'));
                }
            }
        }
        if (Configuration::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND === $merged_config['exceptions_strategy']['out_of_range_page']) {
            $container->register('pagerfanta.event_listener.convert_not_valid_max_per_page_to_not_found', Convert_Not_Valid_Current_Page_To_Not_Found_Listener::class)->add_tag('kernel.event_listener', ['event' => Kernel_Events::EXCEPTION, 'method' => 'onKernelException', 'priority' => 512]);
        }
        if (Configuration::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND === $merged_config['exceptions_strategy']['not_valid_current_page']) {
            $container->register('pagerfanta.event_listener.convert_not_valid_current_page_to_not_found', Convert_Not_Valid_Max_Per_Page_To_Not_Found_Listener::class)->add_tag('kernel.event_listener', ['event' => Kernel_Events::EXCEPTION, 'method' => 'onKernelException', 'priority' => 512]);
        }
    }
    public function prepend(Container_Builder $container): void
    {
        if (!$container->has_extension('twig')) {
            return;
        }
        if (!class_exists(Pagerfanta_Extension::class)) {
            return;
        }
        $refl = new \ReflectionClass(Pagerfanta_Extension::class);
        if (false === $refl->get_file_name()) {
            return;
        }
        $path = \dirname($refl->get_file_name(), 2) . '/templates/';
        $container->prepend_extension_config('twig', ['paths' => [$path => 'Pagerfanta']]);
    }
}