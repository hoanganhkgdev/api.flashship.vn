<?php
$json = json_decode(file_get_contents('storage/app/firebase-auth.json'), true);
$key = $json['private_key'];
$res = openssl_pkey_get_private($key);
if ($res === false) {
    echo "ERROR: OpenSSL could not read the private key.\n";
    while ($msg = openssl_error_string())
        echo $msg . "\n";
} else {
    echo "SUCCESS: OpenSSL successfully read the private key.\n";
}
