<?php

declare(strict_types=1);

namespace Hydra\Nyholm;

use Hydra\Http\Contracts\ServerRequestProviderInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Adapts nyholm's ServerRequestCreator to Hydra's request-provider seam.
 *
 * This is the one place the framework names a concrete PSR-7 implementation:
 * the http package stays free of any PSR-7 vendor, and an app that wants a
 * different PSR-7 library binds its own ServerRequestProviderInterface instead
 * of requiring this package at all.
 */
final class NyholmRequestProvider implements ServerRequestProviderInterface
{
    public function __construct(private readonly ServerRequestCreator $creator) {}

    /**
     * Build a provider from a single Psr17Factory, which implements all four
     * PSR-17 factory interfaces ServerRequestCreator needs. Spares the caller
     * from importing ServerRequestCreator just to wire it up.
     */
    public static function create(Psr17Factory $factory): self
    {
        return new self(new ServerRequestCreator($factory, $factory, $factory, $factory));
    }

    public function fromGlobals(): ServerRequestInterface
    {
        return $this->creator->fromGlobals();
    }
}
