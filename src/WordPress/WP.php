<?php
    
    namespace ValkyriWeb\WPRemoteAuth\WordPress;
    
    class WP
    {
        private string $tableName;
        
        public function __construct()
        {
            $this->tableName = 'sales_plugin_tokens';
        }
    
        /**
         * @throws \Exception
         */
        public function init()
        {
            if ($this->checkIfTablesInitiated()) {
                return 'WordPress Tables Exist';
            }

            $this->generateWordPressTables();
            $this->registerAjaxEndpoints();
            
            return 'WordPress tables generated';
        }
        
        private function generateWordPressTables()
        {
            global $wpdb;
    
            if (!$wpdb) {
                Throw new \Exception('WordPress database not found');
            }
            
            $charset_collate = $wpdb->get_charset_collate();
            
            $table_name = $wpdb->prefix . $this->tableName;
    
            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                user_id mediumint(9) NOT NULL UNIQUE,
                token varchar(255) NOT NULL,
                date_created datetime NOT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
            dbDelta($sql);
        }
    
        public function getTableName()
        {
            return $this->tableName;
        }
    
        public function checkTokenExists($user_id)
        {
            global $wpdb;
            
            $table_name = $wpdb->prefix . $this->tableName;
            
            $token_exists = $wpdb->get_var("SELECT token FROM $table_name WHERE user_id = $user_id");
            
            if ($token_exists) {
                return $token_exists;
            }
            
            return false;
        }
    
        public function registerAjaxEndpoints(): RegisterAjaxEndpoints
        {
            return new RegisterAjaxEndpoints();
        }
    
        private function saveToken($access_token, $user_id)
        {
            global $wpdb;
        
            $table_name = $wpdb->prefix . 'sales_plugin_tokens';
        
            $wpdb->insert(
                $table_name,
                [
                    'user_id' => $user_id,
                    'token' => $access_token,
                    'date_created' => date('Y-m-d H:i:s'),
                ]
            );
        }

        private function deleteToken($user_id)
        {
            global $wpdb;

            $table_name = $wpdb->prefix . 'sales_plugin_tokens';

            $wpdb->delete($table_name, ['user_id' => $user_id]);
        }

        public function checkIfTablesInitiated()
        {
            global $wpdb;

            $table_name = $wpdb->prefix . $this->tableName;

            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");

            if ($table_exists) {
                return true;
            }

            return false;
        }
    }