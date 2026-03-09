<?php
// index.php - الراوتر الرئيسي

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-API-Key, Authorization");

// للطلبات التجريبية
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// تحليل المسار
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

// إزالة مسار الملف من URI
$path = str_replace(dirname($script_name), '', $request_uri);
$path = parse_url($path, PHP_URL_PATH);
$path = ltrim($path, '/');

// تقسيم المسار
$segments = explode('/', $path);
$endpoint = $segments[0] ?? '';

// خريطة المسارات
$routes = [
    'get_codes' => 'endpoints/get_codes.php',
    'add_number' => 'endpoints/add_number.php',
    'delete_number' => 'endpoints/delete_number.php',
    'receive_sms' => 'endpoints/receive_sms.php',
    'stats' => 'endpoints/stats.php',
    'api.php' => 'endpoints/get_codes.php', // للتوافق مع الرابط القديم
];

// التحقق من وجود المسار
if (isset($routes[$endpoint])) {
    require $routes[$endpoint];
} else {
    // صفحة الترحيب
    require_once __DIR__ . '/api/functions.php';';
    sendResponse([
        'name' => 'SMS API Server',
        'version' => '1.0.0',
        'endpoints' => [
            'get_codes' => 'GET - جلب الأكواد الجديدة',
            'add_number' => 'POST - إضافة رقم جديد',
            'delete_number' => 'POST - حذف رقم',
            'receive_sms' => 'POST - استقبال رسالة جديدة',
            'stats' => 'GET - إحصائيات'
        ],
        'documentation' => '/docs',
        'status' => 'active'
    ]);
}
?>