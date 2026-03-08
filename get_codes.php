<?php
// endpoints/get_codes.php

require_once __DIR__ . '/../api/auth.php';
require_once __DIR__ . '/../api/functions.php';

$user = require_auth();

// تحديد الوقت (آخر 5 دقائق)
$time_limit = date('Y-m-d H:i:s', strtotime('-5 minutes'));

// جلب الرسائل الجديدة
$stmt = db()->prepare("
    SELECT m.*, n.country_code 
    FROM messages m
    LEFT JOIN numbers n ON m.number_id = n.id
    WHERE m.received_at > ?
    ORDER BY m.received_at DESC
");
$stmt->execute([$time_limit]);
$messages = $stmt->fetchAll();

$codes = [];
foreach ($messages as $msg) {
    $codes[] = [
        'number' => $msg['number'],
        'sms' => $msg['message'],
        'otp' => $msg['otp_code'],
        'service' => $msg['service'],
        'time' => $msg['received_at'],
        'country' => $msg['country_code']
    ];
}

sendSuccess([
    'codes' => $codes,
    'count' => count($codes)
]);
?>