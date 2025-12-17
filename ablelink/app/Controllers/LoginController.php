<?php
namespace App\Controllers;

use App\Core\Controller;

class LoginController extends Controller {
    public function index(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            if ($email === 'malekjafrar@gmail.com' && $password === 'malek2005') {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = 'Malek Jafrar';
                header('Location: /projetweb/ablelink/');
                exit;
            } else {
                $error = 'Identifiants incorrects';
            }
        }
        $this->render('login', ['error' => $error]);
    }
}

