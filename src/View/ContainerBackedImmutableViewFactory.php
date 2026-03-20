<?php

declare (strict_types=1);
namespace Bab_Dev\Pagerfanta_Bundle\View;

use Bab_Dev\Pagerfanta_Bundle\Exception\Immutable_View_Factory_Exception;
use Pagerfanta\Exception\InvalidArgumentException;
use Pagerfanta\View\View_Factory_Interface;
use Pagerfanta\View\View_Interface;
use Psr\Container\Container_Interface;
final class Container_Backed_Immutable_View_Factory implements View_Factory_Interface
{
    /**
     * @param array<string, string> $serviceMap
     */
    public function __construct(private readonly Container_Interface $container, private readonly array $service_map)
    {
    }
    /**
     * @param array<string, ViewInterface> $views
     *
     * @throws ImmutableViewFactoryException
     */
    public function add(array $views): never
    {
        throw new Immutable_View_Factory_Exception(\sprintf('"%s" cannot be modified after instantiation.', self::class));
    }
    /**
     * @return array<string, ViewInterface>
     */
    public function all(): array
    {
        $views = [];
        foreach (array_keys($this->service_map) as $view_name) {
            $views[$view_name] = $this->get($view_name);
        }
        return $views;
    }
    /**
     * @throws ImmutableViewFactoryException
     */
    public function clear(): never
    {
        throw new Immutable_View_Factory_Exception(\sprintf('"%s" cannot be modified after instantiation.', self::class));
    }
    /**
     * @throws InvalidArgumentException if the view does not exist
     */
    public function get(string $name): View_Interface
    {
        if (!$this->has($name)) {
            throw new InvalidArgumentException(\sprintf('The view "%s" does not exist.', $name));
        }
        return $this->container->get($name);
    }
    public function has(string $name): bool
    {
        return $this->container->has($name);
    }
    /**
     * @throws ImmutableViewFactoryException
     */
    public function remove(string $name): never
    {
        throw new Immutable_View_Factory_Exception(\sprintf('"%s" cannot be modified after instantiation.', self::class));
    }
    /**
     * @throws ImmutableViewFactoryException
     */
    public function set(string $name, View_Interface $view): never
    {
        throw new Immutable_View_Factory_Exception(\sprintf('"%s" cannot be modified after instantiation.', self::class));
    }
}