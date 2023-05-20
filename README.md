# Authentication Token Package

This package provides a simple way to generate and validate authentication tokens using PHP, mainly used for my own projects, but thought it may be useful for other PHP developers out there, contributions welcome.

## Installation

Install the latest version with

```bash
$ composer require lue/authentication-token
```

## Basic Usage

```php
<?php
    
    use Lue\AuthenticationToken\WPRemoteAuth;

    $token = new WPRemoteAuth();
    
    // Initialise the Class
    $args = [
        'wordpress' => true,
        'remote_login_url' => 'http://' . $baseUrl . '/login',
        'remote_register_url' => 'http://' . $baseUrl . '/register',
        'remote_logout_url' => 'http://' . $baseUrl . '/logout',
    ];
    
    $token->init($args);
    
    // Generate a token using existing user and store it in the session / database
    $args = [
        'username' => 'test',
        'password' => 'test',
        'user_id' => 'wordpress_user_id',
    ];
    
    $token->login($args);
    
    // Generate a token and store it in the session / database
    $args = [
        'name' => 'test',
        'email' => 'test',
        'password' => 'test',
        'user_id' => 'wordpress_user_id',
    ];
    
    $token->register($args);
    
    // Delete local tokens based on the current user
    $user_id = 'wordpress_user_id';
    
    $token->logout($user_id);
    
    // Save Token
    $token = $token->generate();
    $user_id = 'wordpress_user_id';
    
    $token->saveToken($token, $user_id);
    
    // Check if token exists in DB
    // Returns True or False
    $token->checkTokenExists();
    
    // Generate a token
    // Returns a token
    $token->generate();
    
    // Validate a token
    $token->validate($token);

```

## Testing

``` bash
$ composer test
```

Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.
