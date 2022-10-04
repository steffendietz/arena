<?php

/**
 * This file is part of Spiral package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spiral\Translator\Translator;

class LocaleSelector implements MiddlewareInterface
{
    /**
     * @var string[]
     */
    private readonly array $availableLocales;

    public function __construct(
        private readonly Translator $translator
    ) {
        $this->availableLocales = $this->translator->getCatalogueManager()->getLocales();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $defaultLocale = $this->translator->getLocale();

        try {
            /** @var string $locale */
            foreach ($this->fetchLocales($request) as $locale) {
                if ($locale !== '' && in_array($locale, $this->availableLocales, true)) {
                    $this->translator->setLocale($locale);
                    break;
                }
            }

            return $handler->handle($request);
        } finally {
            // restore
            $this->translator->setLocale($defaultLocale);
        }
    }

    public function fetchLocales(ServerRequestInterface $request): \Generator
    {
        $header = $request->getHeaderLine('accept-language');
        foreach (explode(',', $header) as $value) {
            if (str_contains($value, ';')) {
                yield substr($value, 0, strpos($value, ';'));
            }

            yield $value;
        }
    }
}
