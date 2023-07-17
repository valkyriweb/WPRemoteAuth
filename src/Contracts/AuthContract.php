<?php

namespace ValkyriWeb\WPRemoteAuth\Contracts;

interface AuthContract
{
    public function init($args = []);

    public function login($username, $password, $user_id);

    public function register($name, $username, $password, $user_id);

    public function logout($user_id);

    public function checkTokenExists($user_id);

    public function generateToken();

    public function validate($token);
}