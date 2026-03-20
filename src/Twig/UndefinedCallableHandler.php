<?php

declare (strict_types=1);
namespace Bab_Dev\Pagerfanta_Bundle\Twig;

use Twig\Error\Syntax_Error;
final class Undefined_Callable_Handler
{
    /**
     * @var string[]
     */
    private const SUPPORTED_FUNCTIONS = ['pagerfanta', 'pagerfanta_page_url'];
    /**
     * @throws SyntaxError if the undefined function is supported by this handler
     */
    public function on_undefined_function(string $name): bool
    {
        if (!\in_array($name, self::SUPPORTED_FUNCTIONS, true)) {
            return false;
        }
        throw new Syntax_Error(\sprintf('Unknown function "%s". Did you forget to run "composer require pagerfanta/twig"?', $name));
    }
}