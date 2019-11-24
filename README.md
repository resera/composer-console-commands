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

Generate resource (Model + Repository + migration + seeder)

```php artisan generate:resource {name} --table={tableName}```

Generate service

```php artisan generate:service --subsystem={subsystem}```

Generate formatter

```php artisan generate:formatter --subsystem={subsystem}```

Generate validator

```php artisan generate:validator --subsystem={subsystem}```
