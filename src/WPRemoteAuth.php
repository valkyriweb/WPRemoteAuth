<?php
    
    namespace ValkyriWeb\WPRemoteAuth;
    
    use GuzzleHttp\Client;
    use Lue\WPRemoteAuth\Contracts\AuthContract;

    class WPRemoteAuth implements AuthContract
    {
        public mixed $args;
        
        public $tableName;
        
        public $WP;
        
        public Client $HTTPClient;
        
        public function __construct()
        {
            $this->HTTPClient = new Client();
        }
        
        public function init($args = [])
        {
            $this->setArgs($args);
            
            if ($this->args['wordpress'] === true) {
                $this->tableName = $this->wordPressInstall();
                $this->WP        = new WordPress\WP();
            }
            
        }
    
        public function wordPressInstall(): string
        {
            try {
                $wpInstall = new WordPress\WP();
                $wpInstall->init();
                
                return $wpInstall->getTableName();
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    
        public function setArgs(mixed $args): void
        {
            $this->args = $args;
        }
    
        public function login($username, $password, $user_id)
        {
            if (empty($username) || empty($password)) {
                return 'Username or password is empty';
            }
            
            if(!$user_id) {
                return 'User ID is empty';
            }
            
            try {
                $tokenExists = $this->checkTokenExists($user_id);
                
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
    
        public function register($name, $email, $password, $user_id)
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
                    $this->saveToken($response->access_token, $user_id);
                }
                
                return 'success';
                
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    
        public function checkTokenExists($user_id)
        {
            return $this->WP->checkTokenExists($user_id);
        }
        
        public function saveToken($token, $user_id)
        {
            return $this->WP->saveToken($token, $user_id);
        }

        public function deleteToken($user_id)
        {
            return $this->WP->deleteToken($user_id);
        }

        public function logout($user_id)
        {
            try {
                $response = $this->HTTPClient->post($this->args['remote_logout_url']);

                $response = json_decode($response->getBody()->getContents());

                $this->deleteToken($user_id);

                return 'success';

            } catch (\Exception $e) {
                echo $e->getMessage();
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