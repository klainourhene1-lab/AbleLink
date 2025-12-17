<?php
namespace App\Controllers;

class LogoutController {
    public function index(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        session_destroy();
        header('Location: /projetweb/ablelink/admin-login');
        exit;
    }
}

