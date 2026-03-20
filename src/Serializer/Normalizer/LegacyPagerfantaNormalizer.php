<?php

declare (strict_types=1);
namespace Bab_Dev\Pagerfanta_Bundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\Cacheable_Supports_Method_Interface;
use Symfony\Component\Serializer\Normalizer\Normalizer_Aware_Interface;
use Symfony\Component\Serializer\Normalizer\Normalizer_Interface;
/**
 * Decorator for {@see PagerfantaNormalizer} implementing the legacy {@CacheableSupportsMethodInterface} for older Symfony version support.
 *
 * @internal
 */
final class Legacy_Pagerfanta_Normalizer implements Normalizer_Interface, Cacheable_Supports_Method_Interface, Normalizer_Aware_Interface
{
    public function __construct(private readonly Pagerfanta_Normalizer $normalizer)
    {
    }
    public function set_normalizer(Normalizer_Interface $normalizer): void
    {
        $this->normalizer->set_normalizer($normalizer);
    }
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        return $this->normalizer->normalize($object, $format, $context);
    }
    public function supports_normalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $this->normalizer->supports_normalization($data, $format, $context);
    }
    public function has_cacheable_supports_method(): bool
    {
        return true;
    }
}