<?php
// install.php - شغله مرة واحدة وبعدين احذفه

require_once 'api/config.php';
require_once 'api/db.php';

echo "<pre>";
echo "🚀 بدء تثبيت نظام SMS API...\n\n";

try {
    // قراءة ملف SQL
    $sql = file_get_contents('install.sql');
    
    // تقسيم الأوامر
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    $conn = db();
    
    foreach ($queries as $query) {
        if (!empty($query)) {
            $conn->exec($query);
            echo "✓ تم تنفيذ: " . substr($query, 0, 50) . "...\n";
        }
    }
    
    echo "\n✅ تم تثبيت قاعدة البيانات بنجاح!\n\n";
    
    // إنشاء كلمة مرور للمشرف
    $admin_pass = bin2hex(random_bytes(4));
    $hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hashed_pass]);
    
    echo "📝 بيانات الدخول:\n";
    echo "  • المستخدم: admin\n";
    echo "  • كلمة المرور: {$admin_pass}\n";
    echo "  • API Key: sk_cc1480ac5e3a4818e07fb4b0674bc2a72228372220dba26ac4579cfd4eda903b\n\n";
    
    echo "🔗 روابط مهمة:\n";
    echo "  • لوحة التحكم: " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/admin/\n";
    echo "  • API الرئيسي: " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/api.php\n\n";
    
    echo "⚠️  مهم: احذف ملف install.php بعد التثبيت!\n";
    
} catch(PDOException $e) {
    echo "❌ خطأ في التثبيت: " . $e->getMessage() . "\n";
}
echo "</pre>";
?>