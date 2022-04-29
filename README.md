# Write fluent policies in Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/soyhuce/laravel-fluent-policy.svg?style=flat-square)](https://packagist.org/packages/soyhuce/laravel-fluent-policy)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/soyhuce/laravel-fluent-policy/run-tests?label=tests)](https://github.com/soyhuce/laravel-fluent-policy/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/soyhuce/laravel-fluent-policy/Check%20&%20fix%20styling?label=code%20style)](https://github.com/soyhuce/laravel-fluent-policy/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![GitHub PHPStan Action Status](https://img.shields.io/github/workflow/status/soyhuce/laravel-fluent-policy/PHPStan?label=phpstan)](https://github.com/soyhuce/laravel-fluent-policy/actions?query=workflow%3APHPStan+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/soyhuce/laravel-fluent-policy.svg?style=flat-square)](https://packagist.org/packages/soyhuce/laravel-fluent-policy)

Write your policies fluently in Laravel.

```php
<?php

class PostPolicy extends FluentPolicy
{
    public function delete(User $user, Post $post): Response
    {
        return $this->denyWhen($post->user_id !== $user->id)
            ->denyWhen($post->published_at !== null)
            ->allow();
    }
}
```

## Installation

You can install the package via composer:

```bash
composer require soyhuce/laravel-fluent-policy
```

## Usage

The goal of this package is to write your policies more easily in a clean syntax.

For exemple, the following policy:

```php
<?php
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, Post $post): bool
    {
        if ($post->user_id !== $user->id) {
            return false;
        }
        
        if ($post->published_at !== null) {
            return false;
        }
    
        return true;
    }
}
```

can be re-written as:

```php
<?php
use Illuminate\Auth\Access\Response;
use Soyhuce\FluentPolicy\FluentPolicy;

class PostPolicy extends FluentPolicy
{
    public function delete(User $user, Post $post): Response
    {
         return $this->denyWhen($post->user_id !== $user->id)
            ->denyWhen($post->published_at !== null)
            ->allow();
    }
}
```

You can customise the response if needed :

```php
return $this->denyWhen($post->published_at !== null, 'You cannot delete a published post')
    ->allow();
```

You can also call another policy or gate this way :

```php
return $this->authorize($user, 'update', $post)
    ->allowWhen($post->published_at === null)
    ->deny();
```

### Lazy evaluation

The different branches `allowWhen` and `denyWhen` are evaluated lazily which mean that the following code is completely
correct :

```php
<?php
use Illuminate\Auth\Access\Response;
use Soyhuce\FluentPolicy\FluentPolicy;

class PostPolicy extends FluentPolicy
{
    public function delete(User $user, Post $post): Response
    {
         // Here, $post->published_at is Carbon or null
    
         return $this->denyWhen($post->user_id !== $user->id)
            ->allowWhen($post->published_at === null) // 1
            ->allowWhen($post->published_at->isFuture()) // 2
            ->deny();
    }
}
```

`2` will only be called if previous branches are all false. We are sure that here `$post->published_at` is not null
thanks to `1`.

## PHPStan

When running PHPStan on

```php
public function delete(User $user, Post $post): Response
{
     return $this->denyWhen($post->user_id !== $user->id)
        ->allowWhen($post->published_at === null) // 1
        ->allowWhen($post->published_at->isFuture()) // 2
        ->deny();
}
```

an error is raised on `2` (`Cannot call method `isFuture` on Carbon|null`).

An extension is available to fix this issue and should be included in your `phpstan.neon` file.

```neon
includes:
    - vendor/bin/soyhuce/laravel-fluent-policies/extension.neon
```

Unfortunately, due to a PHPStan limitation, you still have to rewrite your policy a little bit :

```php
public function delete(User $user, Post $post): Response
{
    $this->denyWhen($post->user_id !== $user->id)
        ->allowWhen($post->published_at === null);
    // From here, PHPStan understands that $post->published_at is not null
    
    return $this->allowWhen($post->published_at->isFuture())
        ->deny();
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Bastien Philippe](https://github.com/bastien-phi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
