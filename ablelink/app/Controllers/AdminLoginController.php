<?php
namespace App\Controllers;

use App\Core\Controller;

class AdminLoginController extends Controller {
    public function index(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $admin_user = 'admin';
            $admin_pass = 'admin123';
            
            if ($username === $admin_user && $password === $admin_pass) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user'] = $username;
                header('Location: /projetweb/ablelink/admin');
                exit;
            } else {
                $error = '❌ Identifiants incorrects!';
            }
        }
        
        $this->render('admin/login', ['error' => $error]);
    }
}

