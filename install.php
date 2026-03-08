<?php
// install.php - 卮睾賱賴 賲乇丞 賵丕丨丿丞 賵亘毓丿賷賳 丕丨匕賮賴

require_once 'api/config.php';
require_once 'api/db.php';

echo "<pre>";
echo "馃殌 亘丿亍 鬲孬亘賷鬲 賳馗丕賲 SMS API...\n\n";

try {
    // 賯乇丕亍丞 賲賱賮 SQL
    $sql = file_get_contents('install.sql');
    
    // 鬲賯爻賷賲 丕賱兀賵丕賲乇
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    $conn = db();
    
    foreach ($queries as $query) {
        if (!empty($query)) {
            $conn->exec($query);
            echo "鉁� 鬲賲 鬲賳賮賷匕: " . substr($query, 0, 50) . "...\n";
        }
    }
    
    echo "\n鉁� 鬲賲 鬲孬亘賷鬲 賯丕毓丿丞 丕賱亘賷丕賳丕鬲 亘賳噩丕丨!\n\n";
    
    // 廿賳卮丕亍 賰賱賲丞 賲乇賵乇 賱賱賲卮乇賮
    $admin_pass = bin2hex(random_bytes(4));
    $hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hashed_pass]);
    
    echo "馃摑 亘賷丕賳丕鬲 丕賱丿禺賵賱:\n";
    echo "  鈥� 丕賱賲爻鬲禺丿賲: admin\n";
    echo "  鈥� 賰賱賲丞 丕賱賲乇賵乇: {$admin_pass}\n";
    echo "  鈥� API Key: sk_cc1480ac5e3a4818e07fb4b0674bc2a72228372220dba26ac4579cfd4eda903b\n\n";
    
    echo "馃敆 乇賵丕亘胤 賲賴賲丞:\n";
    echo "  鈥� 賱賵丨丞 丕賱鬲丨賰賲: " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/admin/\n";
    echo "  鈥� API 丕賱乇卅賷爻賷: " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/api.php\n\n";
    
    echo "鈿狅笍  賲賴賲: 丕丨匕賮 賲賱賮 install.php 亘毓丿 丕賱鬲孬亘賷鬲!\n";
    
} catch(PDOException $e) {
    echo "鉂� 禺胤兀 賮賷 丕賱鬲孬亘賷鬲: " . $e->getMessage() . "\n";
}
echo "</pre>";
?>