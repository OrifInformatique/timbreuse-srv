<?php

function load_key() {
    $fileText = file_get_contents('../.key.json');
    return json_decode($fileText, true)['key'];
}

function create_token(string ...$texts)
{
    $concat_text = '';
    foreach ($texts as $text) {
        $concat_text .= $text;
    }
    helper('UtilityFunctions');
    $key = load_key();
    $token_text = hash_hmac('sha256', $concat_text, $key);
    return $token_text;
}

function is_admin()
{
    return session()->get('user_access') == config('\User\Config\UserConfig')
        ->access_lvl_admin;
}

function get_ci_user_id()
{
    return session()->get('user_id');
}
