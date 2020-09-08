<?php
// モザイク関連の関数

function has_mosaic( $mosaic, $amount, $address ) {
    $has_mosaic = false;
    $validchars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $apiurl = 'accounts/';
    $address = str_replace('-', '', $address);
    if (is_valid_address($address)) {
        $apiurl .= $address;
        $data = get_symbol_api_data($apiurl);
    } else {
        error_log("error : $address is not a valid account.");
        return false;
    }
    $json = json_decode($data, true);
    foreach ($json['account']['mosaics'] as $array) {
        if ($array['id'] == $mosaic && $array['amount'] >= $amount) {
            $has_mosaic = true;
            return $has_mosaic;
        }
    }
    return $has_mosaic;

}
