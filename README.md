# Instructions

Install package

```composer require resera/console-commands```

Add Provider in app.php

```Resera\ConsoleCommands\ConsoleCommandsServiceProvider::class,```

## Commands

Generate boilerplate code

```php artisan generate:boilerplate```

Generate subsystem folders

```php artisan generate:subsystem {name}```

Example:

```php artisan generate:subsystem GeneralSubsystem```

Generate resource (Model + Repository + migration + seeder)

```php artisan generate:resource {name} --table={tableName}```

Example:

```php artisan generate:resource ClientTarget --table=client_targets```

Generate service

```php artisan generate:service {name} --subsystem={subsystem}```

Example:

```php artisan generate:service Book --subsystem=GeneralSubsystem```

Generate formatter

```php artisan generate:formatter {name} --subsystem={subsystem}```

Example:

```php artisan generate:formatter Book --subsystem=GeneralSubsystem```

Generate validator

```php artisan generate:validator {name} --subsystem={subsystem}```

Example:

```php artisan generate:validator Book --subsystem=GeneralSubsystem```

Generate facade

```php artisan generate:facade {name}```

Example:

```php artisan generate:facade Book```