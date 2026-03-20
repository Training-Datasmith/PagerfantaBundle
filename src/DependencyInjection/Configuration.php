<?php

declare (strict_types=1);
namespace Bab_Dev\Pagerfanta_Bundle\Dependency_Injection;

use Symfony\Component\Config\Definition\Builder\Tree_Builder;
use Symfony\Component\Config\Definition\Configuration_Interface;
final class Configuration implements Configuration_Interface
{
    public const EXCEPTION_STRATEGY_CUSTOM = 'custom';
    public const EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND = 'to_http_not_found';
    /**
     * @return TreeBuilder<'array'>
     */
    public function get_config_tree_builder(): Tree_Builder
    {
        /** @var TreeBuilder<'array'> $treeBuilder */
        $tree_builder = new Tree_Builder('babdev_pagerfanta');
        $tree_builder->get_root_node()->children()->scalar_node('default_view')->default_value('default')->end()->scalar_node('default_twig_template')->default_value('@BabDevPagerfanta/default.html.twig')->end()->array_node('exceptions_strategy')->add_defaults_if_not_set()->children()->enum_node('out_of_range_page')->default_value(self::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND)->values([self::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND, self::EXCEPTION_STRATEGY_CUSTOM])->end()->enum_node('not_valid_current_page')->default_value(self::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND)->values([self::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND, self::EXCEPTION_STRATEGY_CUSTOM])->end()->end()->end()->end();
        return $tree_builder;
    }
}