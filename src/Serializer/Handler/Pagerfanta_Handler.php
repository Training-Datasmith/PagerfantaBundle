<?php

declare (strict_types=1);
namespace Bab_Dev\Pagerfanta_Bundle\Serializer\Handler;

use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Graph_Navigator_Interface;
use JMS\Serializer\Handler\Subscribing_Handler_Interface;
use JMS\Serializer\Json_Serialization_Visitor;
use JMS\Serializer\Serialization_Context;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Pagerfanta_Interface;
final class Pagerfanta_Handler implements Subscribing_Handler_Interface
{
    public const PRESERVE_KEYS_KEY = 'pagerfanta_preserve_keys';
    public static function get_subscribing_methods(): array
    {
        return [['direction' => Graph_Navigator_Interface::DIRECTION_SERIALIZATION, 'format' => 'json', 'type' => Pagerfanta::class, 'method' => 'serializeToJson'], ['direction' => Graph_Navigator_Interface::DIRECTION_SERIALIZATION, 'format' => 'json', 'type' => Pagerfanta_Interface::class, 'method' => 'serializeToJson']];
    }
    /**
     * @param PagerfantaInterface<mixed> $pagerfanta
     *
     * @return array<string, mixed>|\ArrayObject<string, mixed>
     */
    public function serialize_to_json(Json_Serialization_Visitor $visitor, Pagerfanta_Interface $pagerfanta, array $type, Serialization_Context $context)
    {
        $items = $pagerfanta->get_current_page_results();
        if ($context->has_attribute(self::PRESERVE_KEYS_KEY)) {
            $preserve_keys = $context->get_attribute(self::PRESERVE_KEYS_KEY);
            if (!\is_bool($preserve_keys) && null !== $preserve_keys) {
                throw new LogicException(\sprintf('The "%s" context key must be a boolean value or null, "%s" given.', self::PRESERVE_KEYS_KEY, get_debug_type($preserve_keys)));
            }
            if (null !== $preserve_keys) {
                // When requiring PHP 8.2, this `is_array()` check can be removed
                if (\is_array($items)) {
                    $items = new \ArrayIterator($items);
                }
                $items = iterator_to_array($items, $preserve_keys);
            }
        }
        return $visitor->visit_array(['items' => $items, 'pagination' => ['current_page' => $pagerfanta->get_current_page(), 'has_previous_page' => $pagerfanta->has_previous_page(), 'has_next_page' => $pagerfanta->has_next_page(), 'per_page' => $pagerfanta->get_max_per_page(), 'total_items' => $pagerfanta->get_nb_results(), 'total_pages' => $pagerfanta->get_nb_pages()]], $type);
    }
}