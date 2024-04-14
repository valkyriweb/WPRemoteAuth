<?php

use ValkyriWeb\WPRemoteAuth\WordPress\RegisterAjaxEndpoints;
use ValkyriWeb\WPRemoteAuth\WPRemoteAuth;
    
require_once 'vendor/autoload.php';

// Example implementation
$WPAuth = new WPRemoteAuth();

$baseUrl = 'test-base-url.test';

$WPAuth->init([
    'wordpress' => true,
    'remote_login_url' => 'https://' . $baseUrl . '/login',
    'remote_register_url' => 'https://' . $baseUrl . '/register',
    'remote_logout_url' => 'https://' . $baseUrl . '/logout',
]);

$name = 'Test User';
$email = 'test@test.com';
$password = 'testpassword';

(new RegisterAjaxEndpoints())();

$WPAuth->login($email, $password);

$WPAuth->register($name, $email, $password);