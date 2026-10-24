<?php
namespace App\Middleware;

class Student {
    public static function handle() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        return true;
    }
}