<?php
// api/functions.php

// إرسال رد JSON
function sendResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// إرسال رد خطأ
function sendError($message, $status = 400) {
    sendResponse([
        'success' => false,
        'error' => $message
    ], $status);
}

// إرسال رد نجاح
function sendSuccess($data = [], $message = '') {
    $response = ['success' => true];
    if ($message) $response['message'] = $message;
    if (!empty($data)) $response['data'] = $data;
    sendResponse($response);
}

// تنظيف الرقم
function cleanNumber($number) {
    return preg_replace('/[^0-9]/', '', $number);
}

// استخراج كود الدولة
function extractCountryCode($number) {
    $number = cleanNumber($number);
    $codes = json_decode(COUNTRY_CODES, true);
    
    // ترتيب الأكواد من الأطول للأقصر
    uksort($codes, function($a, $b) {
        return strlen($b) - strlen($a);
    });
    
    foreach (array_keys($codes) as $code) {
        if (strpos($number, $code) === 0) {
            return $code;
        }
    }
    
    return 'unknown';
}

// استخراج OTP من النص
function extractOTP($message) {
    $patterns = [
        '/\b(\d{4,8})\b/',
        '/code[:\s]*(\d{4,8})/i',
        '/كود[:\s]*(\d{4,8})/u',
        '/otp[:\s]*(\d{4,8})/i',
        '/verification[:\s]*(\d{4,8})/i',
        '/(\d{3})[- ]?(\d{3,4})/',
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $message, $matches)) {
            // إذا كان نمط مقسم
            if (count($matches) > 2) {
                return $matches[1] . $matches[2];
            }
            return $matches[1];
        }
    }
    
    return null;
}

// تحديد الخدمة
function detectService($message) {
    $message_lower = strtolower($message);
    
    $services = [
        'WHATSAPP' => ['whatsapp', 'واتساب', 'واتس'],
        'FACEBOOK' => ['facebook', 'فيسبوك', 'fb'],
        'INSTAGRAM' => ['instagram', 'انستقرام', 'انستا'],
        'TELEGRAM' => ['telegram', 'تيليجرام'],
        'TWITTER' => ['twitter', 'تويتر', 'x.com'],
        'GOOGLE' => ['google', 'gmail', 'جوجل'],
        'TIKTOK' => ['tiktok', 'tik tok'],
        'SNAPCHAT' => ['snapchat'],
        'UBER' => ['uber'],
        'AMAZON' => ['amazon'],
        'NETFLIX' => ['netflix'],
        'MICROSOFT' => ['microsoft', 'outlook', 'hotmail'],
        'APPLE' => ['apple', 'icloud'],
    ];
    
    foreach ($services as $service => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($message_lower, $keyword) !== false) {
                return $service;
            }
        }
    }
    
    return 'GENERAL';
}

// إخفاء الرقم
function maskNumber($number) {
    if (strlen($number) > 8) {
        return substr($number, 0, 3) . '••••' . substr($number, -4);
    }
    return $number;
}

// التحقق من المدخلات
function validateInput($data, $required) {
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            return false;
        }
    }
    return true;
}

// إنشاء مهمة
function createTask($type, $data) {
    $stmt = db()->prepare("INSERT INTO tasks (type, data) VALUES (?, ?)");
    return $stmt->execute([$type, json_encode($data)]);
}

// الحصول على إحصائيات
function getStats() {
    $stats = [];
    
    // عدد الأرقام
    $stmt = db()->query("SELECT COUNT(*) as total, SUM(is_used) as used FROM numbers WHERE is_deleted = 0");
    $stats['numbers'] = $stmt->fetch();
    
    // عدد الرسائل
    $stmt = db()->query("SELECT COUNT(*) as total FROM messages");
    $stats['messages'] = $stmt->fetchColumn();
    
    // آخر 5 رسائل
    $stmt = db()->query("SELECT * FROM messages ORDER BY received_at DESC LIMIT 5");
    $stats['recent'] = $stmt->fetchAll();
    
    return $stats;
}
?>