<?php
// admin/dashboard.php
session_start();
require_once __DIR__ . '/../api/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$stats = getStats();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - SMS API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Tajawal', sans-serif;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: background 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.2);
        }
        .main-content {
            padding: 30px;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stats-card h3 {
            margin: 0;
            color: #333;
            font-size: 28px;
        }
        .stats-card p {
            color: #666;
            margin: 5px 0 0;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .badge-used {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .badge-not-used {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <div class="sidebar">
                    <h4 class="mb-4 text-center">SMS API</h4>
                    <a href="#" class="active"><i class="fas fa-home me-2"></i>الرئيسية</a>
                    <a href="#"><i class="fas fa-phone me-2"></i>الأرقام</a>
                    <a href="#"><i class="fas fa-envelope me-2"></i>الرسائل</a>
                    <a href="#"><i class="fas fa-chart-bar me-2"></i>إحصائيات</a>
                    <a href="#"><i class="fas fa-cog me-2"></i>الإعدادات</a>
                    <hr>
                    <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>تسجيل خروج</a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <h2 class="mb-4">مرحباً، <?php echo $_SESSION['admin_username']; ?></h2>
                
                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-phone fa-2x mb-3" style="color: #667eea"></i>
                            <h3><?php echo $stats['numbers']['total'] ?? 0; ?></h3>
                            <p>إجمالي الأرقام</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-check-circle fa-2x mb-3" style="color: #28a745"></i>
                            <h3><?php echo $stats['numbers']['used'] ?? 0; ?></h3>
                            <p>الأرقام المستخدمة</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-envelope fa-2x mb-3" style="color: #ffc107"></i>
                            <h3><?php echo $stats['messages'] ?? 0; ?></h3>
                            <p>إجمالي الرسائل</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-clock fa-2x mb-3" style="color: #dc3545"></i>
                            <h3><?php echo date('Y-m-d'); ?></h3>
                            <p>آخر تحديث</p>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Messages -->
                <div class="table-container mt-4">
                    <h5 class="mb-3">آخر الرسائل</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الرقم</th>
                                <th>OTP</th>
                                <th>الخدمة</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['recent'] as $msg): ?>
                            <tr>
                                <td><?php echo $msg['id']; ?></td>
                                <td><?php echo maskNumber($msg['number']); ?></td>
                                <td><span class="badge bg-success"><?php echo $msg['otp_code']; ?></span></td>
                                <td><?php echo $msg['service']; ?></td>
                                <td><?php echo $msg['received_at']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Quick Actions -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="table-container">
                            <h5 class="mb-3">إضافة رقم جديد</h5>
                            <form action="add_number.php" method="POST">
                                <div class="mb-3">
                                    <input type="text" name="number" class="form-control" placeholder="أدخل الرقم" required>
                                </div>
                                <button type="submit" class="btn btn-primary">إضافة</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-container">
                            <h5 class="mb-3">API Keys</h5>
                            <div class="alert alert-info">
                                <code>sk_cc1480ac5e3a4818e07fb4b0674bc2a72228372220dba26ac4579cfd4eda903b</code>
                            </div>
                            <small class="text-muted">استخدم هذا المفتاح في تطبيقك</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>