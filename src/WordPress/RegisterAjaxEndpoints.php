<?php
namespace ValkyriWeb\WPRemoteAuth\WordPress;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class RegisterAjaxEndpoints
{
    public function __invoke()
    {
        add_action('wp_ajax_insert_sales_api_token', [$this, 'insert_sales_api_token']);
        add_action('wp_ajax_nopriv_insert_sales_api_token', [$this, 'insert_sales_api_token']);

        add_action('wp_ajax_check_if_sales_api_token_exists', [$this, 'check_if_sales_api_token_exists']);
        add_action('wp_ajax_nopriv_check_if_sales_api_token_exists', [$this, 'check_if_sales_api_token_exists']);

        add_action('wp_ajax_check_if_token_is_valid', [$this, 'check_if_token_is_valid']);
        add_action('wp_ajax_nopriv_check_if_token_is_valid', [$this, 'check_if_token_is_valid']);

        add_action('wp_ajax_logout_user', [$this, 'logout_user']);
        add_action('wp_ajax_nopriv_logout_user', [$this, 'logout_user']);
    }

    public function insert_sales_api_token($tableName)
    {
        global $wpdb;

        $user_id = esc_sql($_POST['user_id']) ?? null;
        $token = esc_sql($_POST['token']);
        $created_at = esc_sql($_POST['created_at']);
        $table_name = $wpdb->prefix . $tableName;

        $existingToken = $wpdb->get_var("SELECT token FROM $table_name LIMIT 1");

        if ($existingToken) {
            // delete existing token and insert new one
            $wpdb->delete($table_name, array('user_id' => $user_id));
        }
        
        try {
            $wpdb->insert($table_name,
                array(
                    'token' => $token,
                    'user_id' => $user_id,
                    'date_created' => $created_at,
                )
            );
            
            echo json_encode('Token inserted successfully');
        } catch (\Exception $e) {
            echo json_encode($e->getMessage());
        }
        
        exit();
    }

    public function check_if_sales_api_token_exists()
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'sale_sight_plugin_tokens';

        $token = $wpdb->get_var("SELECT token FROM $table_name LIMIT 1");

        echo json_encode($token);
        die;
    }

    function check_if_token_is_valid() {
        global $wpdb;
        
        $base_url = esc_sql($_POST['base_url']);
        $table_name = $wpdb->prefix . 'sale_sight_plugin_tokens';

        $token = $wpdb->get_var("SELECT token FROM $table_name LIMIT 1");

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token",
        ];

        $client = new Client(['headers' => $headers]);

        try {
            // Make a GET request to the base URL with the token as a query parameter
            $response = $client->post('https://' . $base_url . '/api/verify-token');

            // If the response status code is 200, the token is valid
            if ($response->getStatusCode() === 200) {
                wp_send_json_success('Token is valid');
            } else {
                wp_send_json_error('Token is invalid');
            }
        } catch (\Exception $e) {
            // If there was an error making the request, return an error response
            wp_send_json_error($e->getMessage());
        } catch (GuzzleException $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    function logout_user()
    {
        global $wpdb;
        
        $base_url = esc_sql($_POST['base_url']);
        $table_name = $wpdb->prefix . 'sale_sight_plugin_tokens';

        $token = $wpdb->get_var("SELECT token FROM $table_name LIMIT 1");

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token",
        ];

        $client = new Client(['headers' => $headers]);

        try {
            // Make a GET request to the base URL with the token as a query parameter
            $response = $client->post('https://' . $base_url . '/api/logout');

            // If the response status code is 200, the token is valid
            if ($response->getStatusCode() === 200) {
                $wpdb->delete($table_name);

                wp_send_json_success('User successfully logged out');
            } else {
                wp_send_json_error('User could not be logged out');
            }
        } catch (\Exception $e) {
            // If there was an error making the request, return an error response
            wp_send_json_error($e->getMessage());
        }
    }
}