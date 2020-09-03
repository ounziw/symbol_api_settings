<?php
// ショートコードを定義する


function symbol_api_account_sc($atts, $content = NULL)
{
    $atts = shortcode_atts(
        [
            'data' => 'id',
        ], $atts, 'symbol_api_get' );

    $apiurl = 'accounts/';
    $content = str_replace('-', '', $content);

    if (is_valid_address($content)) {
        $apiurl .= $content;
        $data = get_symbol_api_data($apiurl);
        return $data;
    } else {
        error_log("error : $content is not a valid account.");
        return ;
    }
}

add_shortcode('symbol_api_account', symbol_api_account_sc);