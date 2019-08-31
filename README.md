# Instructions

Install package

```composer require resera/console-commands```

Add to config/app.php in **providers** array

```Resera\ConsoleCommands\ConsoleCommandsServiceProvider::class```

Publish command files

```php artisan vendor:publish --tag=resera-console-commands```


