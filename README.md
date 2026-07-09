# Hydra Nyholm

The one place the framework names a concrete PSR-7/PSR-17 implementation.

`hydrakit/http` is deliberately free of any PSR-7 vendor — it depends only on the
PSR interfaces and defines a `ServerRequestProviderInterface` seam for building
the incoming request from the environment. This package is the default adapter
that fills that seam with [nyholm/psr7](https://github.com/Nyholm/psr7).

## Usage

The app's composition root registers `NyholmServiceProvider` explicitly — this
is the one line where an app names its PSR-7 vendor:

```php
use Hydra\Nyholm\NyholmServiceProvider;

Kernel::application($container, $basePath)
    ->register(new NyholmServiceProvider)   // fills the PSR-7/17 seams
    ->register(new HttpServiceProvider(/* ... */))
    ->register(new AppServiceProvider);
```

The provider binds `Psr17Factory` (which implements every PSR-17 factory
interface) as the `ResponseFactoryInterface`/`StreamFactoryInterface` the rest
of the app resolves, and binds `ServerRequestProviderInterface` to the adapter:

```php
use Hydra\Nyholm\NyholmRequestProvider;
use Nyholm\Psr7\Factory\Psr17Factory;

$factory  = new Psr17Factory;
$provider = NyholmRequestProvider::create($factory);

$request = $provider->fromGlobals(); // a PSR-7 ServerRequestInterface
```

## Swapping it out

The kernel's `HttpServiceProvider` consumes only the PSR-17 factory interfaces
and `Hydra\Http\Contracts\ServerRequestProviderInterface` — it binds no PSR-7
vendor. An app that prefers a different PSR-7 library simply doesn't require
this package: it registers its own provider binding `ResponseFactoryInterface`,
`StreamFactoryInterface`, and `ServerRequestProviderInterface` to its chosen
implementation, in place of `NyholmServiceProvider` in the composition root.
Nothing in the framework reaches for nyholm directly.
