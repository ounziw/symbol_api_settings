<?php
// symbol ノードへ接続して結果を取得する

function get_symbol_api_data($api)
{
    if (!$api) {
        error_log("error : $api is empty.");
        return;
    }
    $url = get_option('symbol_url');
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        error_log('error : option "symbol_url" is not a valid url.');
        return;
    }

    $link = $url . $api;

    $response = wp_remote_get($link);
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        error_log('error' . $error_message);
        $data = false;
    } else if (wp_remote_retrieve_response_code($response) !== 200 && wp_remote_retrieve_response_code($response) !== 202) {
        error_log('response code: ' . wp_remote_retrieve_response_code($response));
        $data = false;
    } else {
        $data = wp_remote_retrieve_body($response);
    }
    return $data;
}
