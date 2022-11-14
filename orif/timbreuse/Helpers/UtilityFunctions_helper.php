<?php
function load_key() {
    $fileText = file_get_contents('../.key.json');
    return json_decode($fileText, true)['key'];
}

function testhelper() {
    return 'testhelper';
}

function create_token(string ...$texts)
{
    $return_text = '';
    foreach ($texts as $text) {
        $return_text.= $text;
    }
    helper('UtilityFunctions');
    $key = load_key();
    $token_text = hash_hmac('sha256', $text, $key);
    return $token_text;
}