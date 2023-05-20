<?php
    
    use ValkyriWeb\WPRemoteAuth\WPRemoteAuth;
    
    require_once 'vendor/autoload.php';
    
    // Example implementation
    $WPAuth = new WPRemoteAuth();

    $baseUrl = 'bermont-sales-api.test';

    $WPAuth->init([
        'wordpress' => true,
        'remote_login_url' => 'http://' . $baseUrl . '/login',
        'remote_register_url' => 'http://' . $baseUrl . '/register',
        'remote_logout_url' => 'http://' . $baseUrl . '/logout',
    ]);

    $name = 'Test User';
    $email = 'test@test.com';
    $password = 'testpassword';
    $user_id = 1;
    
    $WPAuth->login($email, $password, $user_id);

    $WPAuth->register($name, $email, $password, $user_id);