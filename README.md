# Authentication Token Package

This package provides a simple way to generate and validate authentication tokens using PHP, mainly used for my own projects, but thought it may be useful for other PHP developers out there, contributions welcome.

### Current Version: 1.0.6.4

## Installation

Install the latest version with

```bash
$ composer require valkyriweb/wp-remote-auth
```

## Basic Usage

```php
<?php
    
    use ValkyriWeb\WPRemoteAuth\WPRemoteAuth;
    use ValkyriWeb\WPRemoteAuth\WordPress\RegisterAjaxEndpoints;

    $token = new WPRemoteAuth();
    
    // Initialise the Class
    $args = [
        'wordpress' => true,
        'remote_login_url' => 'http://' . $baseUrl . '/api/login',
        'remote_register_url' => 'http://' . $baseUrl . '/api/register',
        'remote_logout_url' => 'http://' . $baseUrl . '/api/logout',
    ];
    
    $token->init($args);
    (new RegisterAjaxEndpoints())();
    
    // Generate a token using existing user and store it in the session / database
    $args = [
        'username' => 'test',
        'password' => 'test',
    ];
    
    $token->login($args);
    
    // Generate a token and store it in the session / database
    $args = [
        'name' => 'test',
        'email' => 'test',
        'password' => 'test',
    ];
    
    $token->register($args);
    
    $token->logout();
    
    // Save Token
    $token = $token->generate();
    $user_id = 'wordpress_user_id';
    
    $token->saveToken($token);
    
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
