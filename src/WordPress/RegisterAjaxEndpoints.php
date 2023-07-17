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

        add_action('wp_ajax_check_if_token_is_valid', [$this, 'check_if_token_is_valid']);
        add_action('wp_ajax_nopriv_check_if_token_is_valid', [$this, 'check_if_token_is_valid']);

        add_action('wp_ajax_logout_user', [$this, 'logout_user']);
        add_action('wp_ajax_nopriv_logout_user', [$this, 'logout_user']);

        add_action('wp_ajax_insert_industry', [$this, 'insert_industry_into_table']);
        add_action('wp_ajax_nopriv_insert_industry', [$this, 'insert_industry_into_table']);

        add_action('wp_ajax_get_industries', [$this, 'get_list_of_industries_via_api']);
        add_action('wp_ajax_nopriv_get_industries', [$this, 'get_list_of_industries_via_api']);

        add_action('wp_ajax_get_current_industry', [$this, 'get_current_industry']);
        add_action('wp_ajax_nopriv_get_current_industry', [$this, 'get_current_industry']);
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

    function check_if_token_is_valid() {
        global $wpdb;

        $user_id = esc_sql($_POST['user_id']);
        $base_url = esc_sql($_POST['base_url']);
        $table_name = $wpdb->prefix . 'sales_plugin_tokens';

        $token = $wpdb->get_var("SELECT token FROM $table_name WHERE user_id = $user_id");

        $headers = [
            'Content-Type' => 'application/json',
            'X-Header-Bermont' => 'Iz Ya Boi Lenny',
            'Authorization' => "Bearer $token",
        ];

        $client = new \GuzzleHttp\Client(['headers' => $headers]);

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
        }
    }

    function logout_user()
    {
        global $wpdb;

        $user_id = esc_sql($_POST['user_id']);
        $base_url = esc_sql($_POST['base_url']);
        $table_name = $wpdb->prefix . 'sales_plugin_tokens';

        $token = $wpdb->get_var("SELECT token FROM $table_name WHERE user_id = $user_id");

        $headers = [
            'Content-Type' => 'application/json',
            'X-Header-Bermont' => 'Iz Ya Boi Lenny',
            'Authorization' => "Bearer $token",
        ];

        $client = new \GuzzleHttp\Client(['headers' => $headers]);

        try {
            // Make a GET request to the base URL with the token as a query parameter
            $response = $client->post('https://' . $base_url . '/api/logout');

            // If the response status code is 200, the token is valid
            if ($response->getStatusCode() === 200) {
                $wpdb->delete($table_name, array('user_id' => (int)$user_id));

                wp_send_json_success('User successfully logged out');
            } else {
                wp_send_json_error('User could not be logged out');
            }
        } catch (\Exception $e) {
            // If there was an error making the request, return an error response
            wp_send_json_error($e->getMessage());
        }
    }

    function insert_industry_into_table()
    {
        global $wpdb;

        $user_id = esc_sql($_POST['user_id']);
        $industry = esc_sql($_POST['industry']);
        $table_name = $wpdb->prefix . 'sales_plugin_industries';

        $currentIndustry = $wpdb->get_var("SELECT industry FROM $table_name WHERE user_id = $user_id");

        if ($currentIndustry) {
            $wpdb->delete($table_name,  array('user_id' => (int)$user_id));
        }

        $wpdb->insert($table_name,
            array(
                'user_id' => $user_id,
                'industry' => $industry,
            )
        );

        wp_send_json($currentIndustry);

        exit();
    }

    function get_list_of_industries_via_api()
    {
        global $wpdb;

        $user_id = esc_sql($_POST['user_id']);
        $base_url = esc_sql($_POST['base_url']);
        $table_name = $wpdb->prefix . 'sales_plugin_tokens';

        $token = $wpdb->get_var("SELECT token FROM $table_name WHERE user_id = $user_id");

        $headers = [
            'Content-Type' => 'application/json',
            'X-Header-Bermont' => 'Iz Ya Boi Lenny',
            'Authorization' => "Bearer $token",
        ];

        $client = new \GuzzleHttp\Client(['headers' => $headers]);

        try {
            // Make a GET request to the base URL with the token as a query parameter
            $response = $client->get('https://' . $base_url . '/api/get-industries-list');

            // If the response status code is 200, the token is valid
            if ($response->getStatusCode() === 200) {

                $industries = $response->getBody()->getContents();
                wp_send_json($industries);
            } else {
                wp_send_json_error('No industries found');
            }
        } catch (\Exception $e) {
            // If there was an error making the request, return an error response
            wp_send_json_error($e->getMessage());
        }
    }

    function get_current_industry()
    {
        global $wpdb;

        $user_id = esc_sql($_POST['user_id']);
        $table_name = $wpdb->prefix . 'sales_plugin_industries';

        $industry = $wpdb->get_var("SELECT industry FROM $table_name WHERE user_id = $user_id");

        wp_send_json($industry);
//            echo json_encode($industry);
        die;
    }
}