# Infinety LogManager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/krato1/logmanager.svg?style=flat-square)](https://packagist.org/packages/Dick/logmanager)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/krato1/logmanager/master.svg?style=flat-square)](https://travis-ci.org/dick/logmanager)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/krato1/logmanager.svg?style=flat-square)](https://scrutinizer-ci.com/g/dick/logmanager/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/krato1/logmanager.svg?style=flat-square)](https://scrutinizer-ci.com/g/dick/logmanager)
[![Total Downloads](https://img.shields.io/packagist/dt/krato1/logmanager.svg?style=flat-square)](https://packagist.org/packages/Dick/logmanager)

Laravel 5 interface to preview, download and delete Laravel log files.

## Install

Via Composer

``` bash
$ composer require infinety-es/logmanager
```

Then add the service provider to your config/app.php file:

``` 
'Infinety\LogManager\LogManagerServiceProvider',
```

## Usage

Add a menu element for it:

``` php
[
    'label' => "Logs",
    'route' => 'admin/log',
    'icon' => 'fa fa-terminal',
],
```

Or just try at **your-project-domain/admin/log**


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email :author_email instead of using the issue tracker.

## Credits

- [Cristian Tabacitu](https://github.com/tabacitu)
- [Infinety](http:://infinety.es)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
