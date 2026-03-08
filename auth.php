<?php
// api/auth.php

require_once __DIR__ . '/db.php';

class Auth {
    
    // التحقق من مفتاح API
    public static function validateKey($api_key) {
        if (empty($api_key)) {
            return false;
        }
        
        // التحقق من المفاتيح المسموح بها
        $allowed_keys = json_decode(API_ALLOWED_KEYS, true);
        if (in_array($api_key, $allowed_keys)) {
            return ['id' => 0, 'username' => 'api_user'];
        }
        
        // أو التحقق من قاعدة البيانات
        $stmt = db()->prepare("SELECT id, username FROM users WHERE api_key = ? AND is_active = 1");
        $stmt->execute([$api_key]);
        $user = $stmt->fetch();
        
        if ($user) {
            self::logActivity('auth_success', 'User: ' . $user['username']);
            return $user;
        }
        
        self::logActivity('auth_failed', 'Invalid key: ' . $api_key);
        return false;
    }
    
    // التحقق من اسم المستخدم وكلمة المرور (للموقع)
    public static function login($username, $password) {
        $stmt = db()->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            
            // تحديث آخر دخول
            $stmt = db()->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$admin['id']]);
            
            self::logActivity('admin_login', 'Admin: ' . $username);
            return true;
        }
        
        self::logActivity('admin_login_failed', 'Username: ' . $username);
        return false;
    }
    
    // تسجيل الأنشطة
    public static function logActivity($action, $details) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $stmt = db()->prepare("INSERT INTO logs (action, details, ip_address, user_agent) VALUES (?, ?, ?, ?)");
        $stmt->execute([$action, $details, $ip, $user_agent]);
    }
    
    // إنشاء مفتاح API جديد
    public static function generateKey($username) {
        $api_key = 'sk_' . bin2hex(random_bytes(32));
        
        $stmt = db()->prepare("INSERT INTO users (username, api_key) VALUES (?, ?)");
        $stmt->execute([$username, $api_key]);
        
        self::logActivity('generate_key', 'User: ' . $username);
        return $api_key;
    }
}

// دالة التحقق السريعة
function require_auth() {
    $headers = getallheaders();
    $api_key = $headers['X-API-Key'] ?? $_GET['api_key'] ?? '';
    
    $user = Auth::validateKey($api_key);
    
    if (!$user) {
        http_response_code(401);
        die(json_encode([
            'success' => false,
            'error' => ERROR_INVALID_KEY
        ]));
    }
    
    return $user;
}
?>