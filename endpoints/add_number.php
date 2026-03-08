<?php
// endpoints/add_number.php

require_once __DIR__ . '/../api/auth.php';
require_once __DIR__ . '/../api/functions.php';

$user = require_auth();

// استقبال البيانات
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $input = $_POST;
}

if (!validateInput($input, ['number'])) {
    sendError(ERROR_MISSING_PARAMS);
}

$number = cleanNumber($input['number']);
$country_code = extractCountryCode($number);

// التحقق من وجود الرقم
$stmt = db()->prepare("SELECT id FROM numbers WHERE number = ?");
$stmt->execute([$number]);

if ($stmt->fetch()) {
    sendError(ERROR_NUMBER_EXISTS);
}

// إضافة الرقم
$stmt = db()->prepare("INSERT INTO numbers (number, country_code) VALUES (?, ?)");
$stmt->execute([$number, $country_code]);

$number_id = db()->lastInsertId();

// تسجيل النشاط
Auth::logActivity('add_number', 'Added: ' . $number);

sendSuccess([
    'number_id' => $number_id,
    'number' => $number,
    'country' => $country_code
], 'تم إضافة الرقم بنجاح');
?>