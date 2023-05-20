<?php
    
    namespace ValkyriWeb\WPRemoteAuth\WordPress;
    
    class RegisterAjaxEndpoints
    {
        public function __invoke()
        {
            add_action('wp_ajax_insert_sales_api_token', [$this, 'insert_sales_api_token']);
            add_action('wp_ajax_nopriv_insert_sales_api_token', [$this, 'insert_sales_api_token']);
            add_action('wp_ajax_check_if_sales_api_token_exists', [$this, 'check_if_sales_api_token_exists']);
            add_action('wp_ajax_nopriv_check_if_sales_api_token_exists', [$this, 'check_if_sales_api_token_exists']);
        }

        public function insert_sales_api_token()
        {
            global $wpdb;

            $user_id = esc_sql($_POST['user_id']);
            $token = esc_sql($_POST['token']);
            $created_at = esc_sql($_POST['created_at']);
            $table_name = $wpdb->prefix . 'sales_plugin_tokens';

            $existingToken = $wpdb->get_var("SELECT token FROM $table_name WHERE user_id = $user_id");

            if ($existingToken) {
                // delete existing token and insert new one
                $wpdb->delete($table_name, array('user_id' => $user_id));
            }

            $wpdb->insert($table_name,
                array(
                    'user_id' => $user_id,
                    'token' => $token,
                    'date_created' => $created_at,
                )
            );
            exit();
        }

        public function check_if_sales_api_token_exists()
        {
            global $wpdb;

            $user_id = esc_sql($_POST['user_id']);
            $table_name = $wpdb->prefix . 'sales_plugin_tokens';

            $token = $wpdb->get_var("SELECT token FROM $table_name WHERE user_id = $user_id");

            echo json_encode($token);
            die;
        }
    }