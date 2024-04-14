<?php

namespace ValkyriWeb\WPRemoteAuth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use ValkyriWeb\WPRemoteAuth\Contracts\AuthContract;
use ValkyriWeb\WPRemoteAuth\WordPress\WP;

class WPRemoteAuth implements AuthContract
{
    public mixed $args;

    public string $tableName;

    public WP $WP;

    public Client $HTTPClient;

    public function __construct()
    {
        $this->HTTPClient = new Client();
    }

    public function init($args = []): string
    {
        $this->setArgs($args);

        $tableName = $this->args['table_name'] ?? null;

        if ($this->args['wordpress'] === true) {
            $this->tableName = $this->wordPressInstall($tableName);
            $this->WP        = new WordPress\WP();
            
            return 'WordPress implementation initiated';
        } else {
            return 'No implementation for non-wordpress sites available yet.';
        }
    }

    public function wordPressInstall($tableName): string
    {
        try {
            $wpInstall = new WordPress\WP($tableName);
            $wpInstall->init();

            return $wpInstall->getTokenTableName();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function setArgs(mixed $args): void
    {
        $this->args = $args;
    }

    public function login($username, $password)
    {
        if (empty($username) || empty($password)) {
            return 'Username or password is empty';
        }

        try {
            $tokenExists = $this->checkTokenExists();

            if (!$tokenExists) {
                $response = $this->HTTPClient->post($this->args['remote_login_url'], [
                    'email' => $username,
                    'password' => $password,
                ]);

                $response = json_decode($response->getBody()->getContents());

                if (isset($response->access_token)) {
                    $this->saveToken($response->access_token, $user_id);
                }

                return 'success';
            }

            return 'success';

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function register($name, $email, $password)
    {
        if (empty($name) || empty($email) || empty($password)) {
            return 'Name, username or password is empty';
        }

        try {
            $response = $this->HTTPClient->post($this->args['remote_register_url'], [
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            $response = json_decode($response->getBody()->getContents());

            if (isset($response->access_token)) {
                $this->saveToken($response->access_token);
            }

            return 'success';

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function checkTokenExists()
    {
        if ($this->args['wordpress'] === true) {
            return $this->WP->checkTokenExists();
        }
        
        return 'No method to check token exists';
    }

    public function saveToken($token, $user_id = null): string
    {
        if ($this->args['wordpress'] === true) {
            return $this->WP->saveToken($token, $user_id);
        }

        return 'No method to save token';
    }

    public function deleteToken(): string
    {
        if ($this->args['wordpress'] === true) {
            try {
                return $this->WP->deleteToken();
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }

        return 'No method to delete token';
    }

    public function logout(): string
    {
        try {
            $response = $this->HTTPClient->post($this->args['remote_logout_url']);

            $response = json_decode($response->getBody()->getContents());
            
            if ($response->status === 'success') {
                $this->deleteToken();
            } else {
                return 'Error logging out';
            }
            
            return 'success';
        } catch (\Exception $e) {
            return $e->getMessage();
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
    }

    public function generateToken()
    {
        // TODO: Implement generateToken() method.
    }

    public function validate($token)
    {
        // TODO: Implement validate() method.
    }
}