<?php

namespace ValkyriWeb\WPRemoteAuth\Contracts;

interface AuthContract
{
    public function init($args = []);

    public function login($username, $password);

    public function register($name, $username, $password);

    public function logout();

    public function checkTokenExists();

    public function generateToken();

    public function validate($token);
}