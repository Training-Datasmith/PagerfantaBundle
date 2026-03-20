# Architecture: PagerfantaBundle

## Purpose

Symfony bundle that integrates the Pagerfanta pagination library with the Symfony framework, providing DI wiring, Twig helpers, serializer support, and automatic not-found conversion for invalid pages.

## Directory Structure

```
src/
  Bab_Dev_Pagerfanta_Bundle.php               Bundle entry point
  DependencyInjection/
    Bab_Dev_Pagerfanta_Extension.php           Loads configuration
    Configuration.php                          Config tree (default view, etc.)
    CompilerPass/
      Register_Pagerfanta_Views_Pass.php        Tags view services into ViewFactory
      Register_Twig_Undefined_Callable_Pass.php Registers Twig pagerfanta() callable
  EventListener/
    Convert_Not_Valid_Current_Page_To_Not_Found_Listener.php  -> 404 on bad page
    Convert_Not_Valid_Max_Per_Page_To_Not_Found_Listener.php  -> 404 on bad per-page
  Exception/
    Immutable_View_Factory_Exception.php
  RouteGenerator/
    Request_Aware_Route_Generator_Factory.php   Builds route generators from current request
    Router_Aware_Route_Generator.php            Generates URLs using Symfony Router
  Serializer/
    Handler/Pagerfanta_Handler.php              JMS Serializer handler
    Normalizer/Pagerfanta_Normalizer.php        Symfony Serializer normalizer
  Twig/
    Undefined_Callable_Handler.php              Handles pagerfanta(pager, view) Twig call
  View/
    Container_Backed_Immutable_View_Factory.php Lazy-loads views from DI container
```

## Key Design Decisions

- **Kernel event listeners** convert Pagerfanta's `NotValidCurrentPageException` / `NotValidMaxPerPageException` to Symfony 404 responses, keeping pagination errors consistent with framework conventions.
- **Immutable view factory**: Once the DI container is compiled, no new views can be registered at runtime, ensuring deterministic rendering.
- **Dual serializer support**: Both Symfony Serializer and JMS Serializer are supported for API responses that include paginated results.

## Extension Points

- Tag services `pagerfanta.view` to register custom pagination views.
- Override `babdev_pagerfanta.default_view` configuration to change the global default view.

## Dependency Flow

```
Twig template: {{ pagerfanta(pager, 'twitter_bootstrap5') }}
  -> Undefined_Callable_Handler
    -> ViewFactory::get('twitter_bootstrap5')
      -> ContainerInterface::get(view_service_id)
    -> View::render(pagerfanta, routeGenerator)
    -> HTML string
```
