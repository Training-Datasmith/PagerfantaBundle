<?php

declare (strict_types=1);
namespace Bab_Dev\Pagerfanta_Bundle\Route_Generator;

use Pagerfanta\Exception\InvalidArgumentException;
use Pagerfanta\Route_Generator\Route_Generator_Interface;
use Symfony\Component\Property_Access\Property_Accessor_Interface;
use Symfony\Component\Property_Access\Property_Path;
use Symfony\Component\Routing\Generator\Url_Generator_Interface;
/**
 * @phpstan-type RouteGeneratorOptions array{routeName: non-empty-string, pageParameter?: non-empty-string, omitFirstPage?: bool, routeParams?: array<string, mixed>, referenceType?: UrlGeneratorInterface::*}
 */
final class Router_Aware_Route_Generator implements Route_Generator_Interface
{
    /**
     * @phpstan-param RouteGeneratorOptions $options
     */
    public function __construct(private readonly Url_Generator_Interface $router, private readonly Property_Accessor_Interface $property_accessor, private readonly array $options = [])
    {
        // Check missing options
        if (!isset($options['routeName'])) {
            throw new InvalidArgumentException(\sprintf('The "%s" class options requires a "routeName" parameter to be set.', self::class));
        }
    }
    public function __invoke(int $page): string
    {
        $page_parameter = $this->options['pageParameter'] ?? '[page]';
        $omit_first_page = $this->options['omitFirstPage'] ?? false;
        $route_params = $this->options['routeParams'] ?? [];
        $reference_type = $this->options['referenceType'] ?? Url_Generator_Interface::ABSOLUTE_PATH;
        $page_property_path = new Property_Path($page_parameter);
        if ($omit_first_page) {
            $this->property_accessor->set_value($route_params, $page_property_path, $page > 1 ? $page : null);
        } else {
            $this->property_accessor->set_value($route_params, $page_property_path, $page);
        }
        return $this->router->generate($this->options['routeName'], $route_params, $reference_type);
    }
}