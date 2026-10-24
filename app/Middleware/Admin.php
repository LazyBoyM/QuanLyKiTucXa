<?php
namespace App\Middleware;

class Admin {
    public static function handle() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        return true;
    }
}