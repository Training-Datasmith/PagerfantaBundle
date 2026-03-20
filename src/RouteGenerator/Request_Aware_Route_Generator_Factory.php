<?php

declare (strict_types=1);
namespace Bab_Dev\Pagerfanta_Bundle\Route_Generator;

use Pagerfanta\Exception\RuntimeException;
use Pagerfanta\Route_Generator\Route_Generator_Factory_Interface;
use Pagerfanta\Route_Generator\Route_Generator_Interface;
use Symfony\Component\Http_Foundation\Request;
use Symfony\Component\Http_Foundation\Request_Stack;
use Symfony\Component\Property_Access\Property_Accessor_Interface;
use Symfony\Component\Routing\Generator\Url_Generator_Interface;
final class Request_Aware_Route_Generator_Factory implements Route_Generator_Factory_Interface
{
    public function __construct(private readonly Url_Generator_Interface $router, private readonly Request_Stack $request_stack, private readonly Property_Accessor_Interface $property_accessor)
    {
    }
    public function create(array $options = []): Route_Generator_Interface
    {
        $options = array_replace(['routeName' => null, 'routeParams' => [], 'pageParameter' => '[page]', 'omitFirstPage' => false], $options);
        if (null === $options['routeName']) {
            $request = $this->get_request();
            if (null === $request) {
                throw new RuntimeException('The request aware route generator can not be used when there is not an active request.');
            }
            if (null !== $this->request_stack->get_parent_request()) {
                throw new RuntimeException('The request aware route generator can not guess the route when used in a sub-request, pass the "routeName" option to use this generator.');
            }
            $options['routeName'] = $request->attributes->get('_route');
            // Make sure we read the route parameters from the passed option array
            $default_route_params = array_merge($request->query->all(), $request->attributes->get('_route_params', []));
            $options['routeParams'] = array_merge($default_route_params, $options['routeParams']);
        }
        return new Router_Aware_Route_Generator($this->router, $this->property_accessor, $options);
    }
    private function get_request(): ?Request
    {
        return $this->request_stack->get_current_request();
    }
}