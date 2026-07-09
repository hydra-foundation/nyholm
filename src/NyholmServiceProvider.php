<?php

declare(strict_types=1);

namespace Hydra\Nyholm;

use Hydra\Core\Contracts\ContainerInterface;
use Hydra\Core\Providers\ServiceProvider;
use Hydra\Http\Contracts\ServerRequestProviderInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Fills Hydra's PSR-7/17 seams with nyholm. Registered explicitly by the app's
 * composition root — this is the one place an app names its PSR-7 vendor. An
 * app that prefers another library registers its own provider (binding the
 * PSR-17 factory interfaces and ServerRequestProviderInterface) and never
 * requires this package.
 */
final class NyholmServiceProvider extends ServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        // nyholm's Psr17Factory implements every PSR-17 factory interface.
        $container->singleton(Psr17Factory::class, fn () => new Psr17Factory);
        $container->singleton(ResponseFactoryInterface::class, fn () => $container->get(Psr17Factory::class));
        $container->singleton(StreamFactoryInterface::class, fn () => $container->get(Psr17Factory::class));

        // Capture the incoming request from PHP globals (nyholm behind our seam).
        $container->singleton(ServerRequestProviderInterface::class, function () use ($container) {
            return NyholmRequestProvider::create($container->get(Psr17Factory::class));
        });
    }
}
