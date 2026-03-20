<?php

declare (strict_types=1);
namespace Bab_Dev\Pagerfanta_Bundle\Serializer\Normalizer;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Pagerfanta_Interface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\Normalizer_Aware_Interface;
use Symfony\Component\Serializer\Normalizer\Normalizer_Aware_Trait;
use Symfony\Component\Serializer\Normalizer\Normalizer_Interface;
final class Pagerfanta_Normalizer implements Normalizer_Interface, Normalizer_Aware_Interface
{
    use Normalizer_Aware_Trait;
    public const PRESERVE_KEYS_KEY = 'pagerfanta_preserve_keys';
    /**
     * @throws InvalidArgumentException when the object given is not a supported type for the normalizer
     * @throws LogicException           when the normalizer is not called in an expected context
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        if (!$object instanceof Pagerfanta_Interface) {
            throw new InvalidArgumentException(\sprintf('The object must be an instance of "%s".', Pagerfanta_Interface::class));
        }
        $items = $object->getIterator();
        if (\array_key_exists(self::PRESERVE_KEYS_KEY, $context)) {
            $preserve_keys = $context[self::PRESERVE_KEYS_KEY];
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
        return ['items' => $this->normalizer->normalize($items, $format, $context), 'pagination' => ['current_page' => $object->get_current_page(), 'has_previous_page' => $object->has_previous_page(), 'has_next_page' => $object->has_next_page(), 'per_page' => $object->get_max_per_page(), 'total_items' => $object->get_nb_results(), 'total_pages' => $object->get_nb_pages()]];
    }
    public function supports_normalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Pagerfanta_Interface;
    }
    /**
     * @return array<class-string, true>
     */
    public function get_supported_types(?string $format): array
    {
        return [Pagerfanta_Interface::class => true, Pagerfanta::class => true];
    }
}