<?php
function load_key() {
    $fileText = file_get_contents('../.key.json');
    return json_decode($fileText, true)['key'];
}

function testhelper() {
    return 'testhelper';
}