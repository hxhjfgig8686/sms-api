<?php
// endpoints/delete_number.php

require_once __DIR__ . '/../api/auth.php';
require_once __DIR__ . '/../api/functions.php';

$user = require_auth();

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $input = $_POST;
}

if (!validateInput($input, ['number'])) {
    sendError(ERROR_MISSING_PARAMS);
}

$number = cleanNumber($input['number']);

// حذف الرقم (soft delete)
$stmt = db()->prepare("UPDATE numbers SET is_deleted = 1 WHERE number = ?");
$stmt->execute([$number]);

if ($stmt->rowCount() > 0) {
    Auth::logActivity('delete_number', 'Deleted: ' . $number);
    sendSuccess([], 'تم حذف الرقم بنجاح');
} else {
    sendError(ERROR_NUMBER_NOT_FOUND);
}
?>