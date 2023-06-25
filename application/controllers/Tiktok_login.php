<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tiktok_login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function index()
    {
        $redirect_uri = base_url('tiktok_login/callback');
        $auth_url = 'https://open-api.tiktok.com/platform/oauth/connect/?client_key=' . config_item('tiktok_app_id') . '&response_type=code&scope=user.info.basic,user.info.avatar&redirect_uri=' . urlencode($redirect_uri);
        redirect($auth_url);
    }

    public function callback()
    {
        $code = $this->input->get('code');
        if (!empty($code)) {
            $access_token_url = 'https://open-api.tiktok.com/platform/oauth/access_token/';
            $params = array(
                'app_id' => config_item('tiktok_app_id'),
                'app_secret' => config_item('tiktok_app_secret'),
                'code' => $code,
                'grant_type' => 'authorization_code',
            );
            $response = json_decode($this->curl_post($access_token_url, $params));
            if (isset($response->data->access_token)) {
                $access_token = $response->data->access_token;
                // Store or use the access token as per your requirement
                // You can also fetch user information using the access token
                $this->load->view('tiktok_login_success');
            } else {
                // Handle error response
                $this->load->view('tiktok_login_error');
            }
        } else {
            // Handle error case where code is empty
            $this->load->view('tiktok_login_error');
        }
    }

    private function curl_post($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
