<?php
/**
 * Plugin Name: symbol api
 * Version: 0.7
 * License: GPL ver.2 or later
 */

function symbol_api_account_sc($atts, $content = NULL)
{
    $atts = shortcode_atts(
        [
            'data' => 'id',
        ], $atts, 'symbol_api_get' );

    $validchars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $apiurl = 'accounts/';
    $content = str_replace('-', '', $content);
    if (strlen($content) == strspn($content, $validchars)) {
        $apiurl .= $content;
        $json = get_symbol_data($apiurl);
        return $json;
    } else {
        error_log('error : $content is not a valid account.');
        return ;
    }
}
add_shortcode('symbol_api_account', symbol_api_account_sc);

function get_symbol_data($api)
{
    if (!$api) {
        error_log('error : $api is empty.');
        return;
    }
    $url = get_option('symbol_url');
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        error_log('error : option "symbol_url" is not a valid url.');
        return;
    }

    $link = $url . $api;

    // 外部URLとの通信
    $response = wp_remote_get($link);
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        error_log('error' . $error_message);
        $data = false;
    } else if (wp_remote_retrieve_response_code($response) !== 200) {
        error_log('response code: ' . wp_remote_retrieve_response_code($response));
        $data = false;
    } else {
        $data = wp_remote_retrieve_body($response);
    }
    return $data;
}

// 設定画面を追加する
function symbol_api_settings_menu()
{
    add_options_page(
        'symbol設定', // ページのタイトル
        'symbol設定', // メニューのタイトル
        'manage_options', // このページを操作する権限
        'symbol_api_settings', // ページ名
        'symbol_api_settings_plugin_options' // コールバック関数。この関数の実行結果が出力される
    );
}

add_action('admin_menu', 'symbol_api_settings_menu');

// フォームの枠組を出力する
function symbol_api_settings_plugin_options()
{
    ?>
    <div class="wrap">
        <form action="options.php" method="post">
            <?php do_settings_sections('symbol_api_settings'); // ページ名
            ?>
            <?php settings_fields('symbol_api_settings-group'); // グループ名
            ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}


// セクションの作成
function symbol_api_settings_init()
{
    add_settings_section(
        'symbol_api_settings_section', // セクション名
        '接続先', // タイトル
        'symbol_api_settings_section_callback_function', // コールバック関数。この関数の実行結果が出力される
        'symbol_api_settings' // このセクションを表示するページ名。do_settings_sectionsで設定
    );
}

add_action('admin_init', 'symbol_api_settings_init');

function symbol_api_settings_section_callback_function()
{
    echo '<p>API設定を行います</p>';
}


// フィールドの追加
function settings_field_url()
{

    add_settings_field(
        'url', // フィールド名
        '接続先url', // タイトル
        'symbol_geturl_callback_function', // コールバック関数。この関数の実行結果が出力される
        'symbol_api_settings', // このフィールドを表示するページ名。do_settings_sectionsで設定
        'symbol_api_settings_section' // このフィールドを表示するセクション名。add_settings_sectionで設定
    );

    register_setting(
        'symbol_api_settings-group', // グループ名。settings_fieldsで設定
        'symbol_url', // オプション名
        'esc_url' // 入力値をサニタイズする関数
    );
}
add_action('admin_init', 'settings_field_url', 15);

// フォーム項目を表示する
function symbol_geturl_callback_function()
{
    echo '<input name="symbol_url" id="symbol_url" type="text" value="';
    form_option('symbol_url');
    echo '" size="70" />';
}
