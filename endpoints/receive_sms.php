<?php
// endpoints/receive_sms.php

require_once __DIR__ . '/../api/auth.php';
require_once __DIR__ . '/../api/functions.php';

$user = require_auth();

$input = json_decode(file_get_contents('php://input'), true);

if (!validateInput($input, ['number', 'message'])) {
    sendError(ERROR_MISSING_PARAMS);
}

$number = cleanNumber($input['number']);
$message = $input['message'];
$otp = extractOTP($message);
$service = detectService($message);

// البحث عن الرقم في قاعدة البيانات
$stmt = db()->prepare("SELECT id FROM numbers WHERE number = ? AND is_deleted = 0");
$stmt->execute([$number]);
$number_data = $stmt->fetch();

$number_id = $number_data ? $number_data['id'] : null;

// إذا كان الرقم موجود، تحديث حالة الاستخدام
if ($number_id) {
    $stmt = db()->prepare("UPDATE numbers SET is_used = 1 WHERE id = ?");
    $stmt->execute([$number_id]);
}

// حفظ الرسالة
$stmt = db()->prepare("
    INSERT INTO messages (number_id, number, message, otp_code, service) 
    VALUES (?, ?, ?, ?, ?)
");
$stmt->execute([$number_id, $number, $message, $otp, $service]);

// إنشاء مهمة للإشعار
if ($otp) {
    createTask('new_otp', [
        'number' => $number,
        'otp' => $otp,
        'message' => $message,
        'service' => $service
    ]);
}

Auth::logActivity('receive_sms', 'Number: ' . $number . ', OTP: ' . $otp);

sendSuccess([
    'message_id' => db()->lastInsertId(),
    'otp' => $otp,
    'service' => $service
], 'تم استلام الرسالة بنجاح');
?>