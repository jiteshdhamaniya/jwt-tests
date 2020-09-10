# Laravel JWT Tests Package for tymon/jwt-auth
[![GitHub issues](https://img.shields.io/github/issues/jiteshdhamaniya/jwt-tests)](https://packagist.org/packages/jiteshdhamaniya/jwt-tests)

[![GitHub Forks](https://img.shields.io/github/forks/jiteshdhamaniya/jwt-tests)](https://packagist.org/packages/jiteshdhamaniya/jwt-tests)

[![GitHub Stars](https://img.shields.io/github/stars/jiteshdhamaniya/jwt-tests)](https://packagist.org/packages/jiteshdhamaniya/jwt-tests)

## Installation
> Before installation please make sure tymon/jwt-auth
> ```bash
> composer require tymon/jwt-auth
> ```

```bash
composer require jiteshdhamaniya/jwt-tests --dev
php artisan make:jwt-tests 
```

Edit `phpunit.xml` file by adding these two lines between `<php>` tags:
```xml
<server name="DB_CONNECTION" value="sqlite"/>
<server name="DB_DATABASE" value=":memory:"/>
```
Alternatively, use different database than sqlite, but also different from the one used for development.

## EndPoint
By Default it uses endpoint ```/api``` if you do have some other endpoint such as ```/api/v1``` you can change using ```endpoint`` option. like this 
> ```bash
> php artisan make:jwt-tests --endpoint=api/v1
> ```

