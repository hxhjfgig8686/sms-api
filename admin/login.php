<?php
// admin/login.php
session_start();
require_once __DIR__ . '/../api/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (Auth::login($username, $password)) {
        header('Location: dashboard.php');
        exit();
    } else {
        header('Location: index.php?error=1');
        exit();
    }
}

header('Location: index.php');
?>