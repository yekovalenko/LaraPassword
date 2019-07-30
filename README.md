**LaraPassword** is a simple Password Manager for Laravel 5.*.

## ABOUT

- [x] Creating passwords
- [x] Creating categories.
- [x] Creating sub-categories.
- [x] Generating random passwords

## INSTALLATION

This package can be installed via [Composer](http://getcomposer.org):

```
composer require yekovalenko/larapassword
```

### Add the Service Provider & Facade/Alias

Once LaraPassword is installed, you need to register the service provider in `config/app.php`.
Make sure to add the following line **above** the `RouteServiceProvider`.

```PHP
Yekovalenko\LaraPassword\LaraPasswordServiceProvider::class,
```

You may add the following `aliases` to your `config/app.php`:

```PHP
'LaraPassword' => Yekovalenko\LaraPassword\Facades\LaraPassword::class,
```

Publish the package config file by running the following command:

```
php artisan vendor:publish --provider="Yekovalenko\LaraPassword\LaraPasswordServiceProvider"
```

## CONFIGURATION
### Migrations

Two new tables will be created for storing categories and passwords:
```
lp_passwords
lp_categories
```

To run the migrations from this package use the following command:

```
php artisan migrate --path="/vendor/yekovalenko/larapassword/src/resources/migrations"
```

### Security Hash

The *Label*, *Login*, *Password* and *Url* fields are hashed in the database.

To create a package security hash, run the next command:

```
php artisan larapassword:hash
```

To generate a package security hash without saving, run the next command:

```
php artisan larapassword:hash --show
```

The hash can also be saved in the `larapassword.php` file in the `config` directory. 
This should be a base64 encoded string, and look like Laravel `APP_KEY`.

## ERRORS

This package throws several exceptions. You are free to use `try/catch`
statements or to rely on the Laravel built-in exceptions handler.

* `BadGeneratorAttributes` exception:

Wrong password generator params.

* `BadHash` exception:

Security hash is not set or is incorrect.

* `InvalidData` exception:

Invalid data provided when creating/updating the password/category.

* `NotFound` exception:

Password or category not found.

## USAGE
### API

Create new category:

* `LaraPassword::addCategory($data)` where `$data` parameter can accept the next fields:
```php
    $data = [
        'parent_id' => 1,
        'title' =>' Test Category',
        'description' => 'Test Category Description',
    ];
```
and will return ID of created category.

Edit category:

* `LaraPassword::editCategory($password_id, $new_data)` where `$data` parameter is similar to the `addCategory()` function.

Remove category:

* `LaraPassword::removeCategory($category_id)`

Get category details:

* `LaraPassword::getCategory($category_id)`

Get categories:

* `LaraPassword::getCategories($parent_id)`. Use `$parent_id` for get categories:
```php
    $parent_id = null; // Get all categories
    $parent_id = 0;    // Get categories that are not assigned to any category
    $parent_id = 1;    // Get sub-categories that are assigned to category with ID = 1
```

Generate a random password:

* `LaraPassword::generate($length, $letters, $numbers, $chars, $uppercase)` where by default:
```PHP
    $length = 12;
    $letters = true;
    $numbers = true;
    $chars = true;
    $uppercase = true;
```

Create new password:

* `LaraPassword::addPassword($data)` where `$data` can accept the next fields:
```php
    $data = [
        'category_id' => 1,
        'label' => 'Test Password',
        'login' => 'root',
        'password' => 'password',
        'url' => 'example.com',
        'description' => 'Test Password Description',
        'metadata' => [
            'key1' => 'value',
            'key2' => 'value2'
        ]
    ];
```
and will return ID of created password.

Edit password:

* `LaraPassword::editPassword($password_id, $new_data)` where `$data` parameter is similar to the `addPassword()` function.

Remove password:

* `LaraPassword::removePassword($password_id)`

Get password details:

* `LaraPassword::getPassword($password_id)`

Get passwords:

* `LaraPassword::getPasswords($category_id)`. Use `$category_id` for get passwords:
```php
    $category_id = null; // Get all passwords
    $category_id = 0;    // Get passwords that are not assigned to any category
    $category_id = 1;    // Get passwords that are assigned to category with ID = 1
```

## CONTRIBUTE

Feel free to comment, contribute and help.

## LICENSE

LaraPassword is licensed under [The MIT License (MIT)](LICENSE).
