# Hygraph Api

[![Latest Version on Packagist](https://img.shields.io/packagist/v/combindma/hygraph-api.svg?style=flat-square)](https://packagist.org/packages/combindma/hygraph-api)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/combindma/hygraph-api/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/combindma/hygraph-api/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/combindma/hygraph-api/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/combindma/hygraph-api/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/combindma/hygraph-api.svg?style=flat-square)](https://packagist.org/packages/combindma/hygraph-api)


## Installation

You can install the package via composer:

```bash
composer require combindma/hygraph-api
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="hygraph-api-config"
```

This is the contents of the published config file:

```php
return [
        'content_api' => env('HYPGRAPH_CONTENT_API'),
        'token' => env('HYPGRAPH_TOKEN'),
        'cache_ttl' => 60 * 60 * 24 * 30,
];
```

## Usage

```php
$hygraphApi = new Combindma\HygraphApi();
echo $hygraphApi->echoPhrase('Hello, Combindma!');
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Combind](https://github.com/combindma)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
