# Laravel 5 BackupManager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/krato1/laravelBackupManager.svg?style=flat-square)](https://packagist.org/packages/krato1/laravelBackupManager)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/krato1/laravelBackupManager/master.svg?style=flat-square)](https://travis-ci.org/krato1/laravelBackupManager)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/krato1/laravelBackupManager.svg?style=flat-square)](https://scrutinizer-ci.com/g/krato1/laravelBackupManager/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/krato1/laravelBackupManager.svg?style=flat-square)](https://scrutinizer-ci.com/g/krato1/laravelBackupManager)
[![Total Downloads](https://img.shields.io/packagist/dt/krato1/laravelBackupManager.svg?style=flat-square)](https://packagist.org/packages/krato1/laravelBackupManager)

Laravel 5 admin interface for manage backups. 

## Install

Via Composer

``` bash
$ composer require infinety-es/laravelBackupManager
```

Then add the service providers to your config/app.php file:

``` 
'Spatie\Backup\BackupServiceProvider',
'Infinety\BackupManager\BackupManagerServiceProvider',
```

Publish the config files:

```
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
php artisan vendor:publish --provider="Infinety\BackupManager\BackupManagerServiceProvider"
```

## Usage

Add a menu element for it:

``` php
[
    'label' => "Backups",
    'route' => 'admin/backup',
    'icon' => 'fa fa-hdd-o',
],
```

Or just try at **your-project-domain/admin/backup**

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email hello@infinety.es instead of using the issue tracker.

## Credits

- [Infinety](https://infinety.es)
- [Spatie](https://github.com/spatie)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
