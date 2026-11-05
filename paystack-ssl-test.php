<?php
$ch = curl_init("https://api.paystack.co");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20,
]);

$response = curl_exec($ch);

if ($response === false) {
    echo "CURL ERROR: " . curl_error($ch) . PHP_EOL;
} else {
    echo "SSL CONNECTION SUCCESSFUL" . PHP_EOL;
    echo "HTTP STATUS: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . PHP_EOL;
}

curl_close($ch);
