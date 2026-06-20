# Hydra Nyholm

The one place the framework names a concrete PSR-7/PSR-17 implementation.

`hydra/http` is deliberately free of any PSR-7 vendor — it depends only on the
PSR interfaces and defines a `ServerRequestProviderInterface` seam for building
the incoming request from the environment. This package is the default adapter
that fills that seam with [nyholm/psr7](https://github.com/Nyholm/psr7).

## Usage

```php
use Hydra\Nyholm\NyholmRequestProvider;
use Nyholm\Psr7\Factory\Psr17Factory;

$factory  = new Psr17Factory; // implements every PSR-17 factory interface
$provider = NyholmRequestProvider::create($factory);

$request = $provider->fromGlobals(); // a PSR-7 ServerRequestInterface
```

The app's service provider binds `ServerRequestProviderInterface` to this, and
binds `Psr17Factory` as the PSR-17 response/stream factory the rest of the app
resolves.

## Swapping it out

Because consumers depend on `Hydra\Http\Contracts\ServerRequestProviderInterface`
(and the PSR-17 factory interfaces), an app that prefers a different PSR-7
library simply doesn't require this package: it binds its own provider and
factories. Nothing in the framework reaches for nyholm directly.
