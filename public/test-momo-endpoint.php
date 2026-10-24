<?php
$endpoints = [
    'https://payment.momo.vn:2443/v2/gateway/api/create',
    'https://test-payment.momo.vn/gw_payment/transactionprocessor',
];

foreach ($endpoints as $url) {
    echo "<h3>Testing: $url</h3>";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode<br>";
    if ($error) {
        echo "Error: $error<br>";
    } else {
        echo "Response: " . htmlspecialchars($result) . "<br>";
    }
    echo "<hr>";
}