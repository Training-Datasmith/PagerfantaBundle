<?php

declare (strict_types=1);
namespace Bab_Dev\Pagerfanta_Bundle\Route_Generator;

use Pagerfanta\Exception\InvalidArgumentException;
use Pagerfanta\Route_Generator\Route_Generator_Interface;
use Symfony\Component\Property_Access\Property_Accessor_Interface;
use Symfony\Component\Property_Access\Property_Path;
use Symfony\Component\Routing\Generator\Url_Generator_Interface;
/**
 * Generates paginated URLs using the Symfony Router component.
 *
 * Accepts a fixed set of route options at construction time and produces
 * absolute or relative URLs for each page number on invocation.
 *
 * Required option: routeName (non-empty-string)
 * Optional options: pageParameter, omitFirstPage, routeParams, referenceType
 *
 * @phpstan-type RouteGeneratorOptions array{routeName: non-empty-string, pageParameter?: non-empty-string, omitFirstPage?: bool, routeParams?: array<string, mixed>, referenceType?: UrlGeneratorInterface::*}
 */
final class Router_Aware_Route_Generator implements Route_Generator_Interface
{
    /**
     * @param Url_Generator_Interface      $router            Symfony Router for URL generation
     * @param Property_Accessor_Interface  $property_accessor Used to inject the page number into route params via property path
     * @param array<string, mixed>         $options           Route generator options (must include 'routeName')
     *
     * @phpstan-param RouteGeneratorOptions $options
     *
     * @throws \Pagerfanta\Exception\InvalidArgumentException If 'routeName' is not set in $options
     */
    public function __construct(
        private readonly Url_Generator_Interface $router,
        private readonly Property_Accessor_Interface $property_accessor,
        private readonly array $options = [],
    ) {
        // Check missing options
        if (!isset($options['routeName'])) {
            throw new InvalidArgumentException(\sprintf('The "%s" class options requires a "routeName" parameter to be set.', self::class));
        }
    }

    /**
     * Generates the URL for the given page number.
     *
     * When omitFirstPage is enabled, page 1 injects null into the route params
     * so that the page parameter is omitted from the generated URL (for clean
     * first-page URLs).
     *
     * @param int $page Page number (1-based)
     *
     * @return string Generated URL (absolute path by default, or as configured by referenceType)
     */
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