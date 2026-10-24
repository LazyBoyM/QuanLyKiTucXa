<?php
// Debug functions
function dd(...$vars) {
    foreach ($vars as $var) {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
    die;
}

function dump(...$vars) {
    foreach ($vars as $var) {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}

// URL functions
function url($path = '') {
    if (empty($path) || $path === '/') {
        return BASE_URL;
    }
    
    $path = ltrim($path, '/');
    return BASE_URL . '/' . $path;
}

function redirect($path) {
    header('Location: ' . url($path));
    exit;
}

function back() {
    $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
    header('Location: ' . $referer);
    exit;
}

// Session functions
function session($key, $default = null) {
    return $_SESSION[$key] ?? $default;
}

function set_session($key, $value) {
    $_SESSION[$key] = $value;
}

function flash($key, $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}

function old($key, $default = '') {
    $old = $_SESSION['flash']['old'] ?? [];
    return $old[$key] ?? $default;
}

// Auth functions
function auth() {
    return $_SESSION['user'] ?? null;
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function is_admin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function is_student() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'student';
}

function is_resident() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'resident';
}

function can($action) {
    if (!is_logged_in()) return false;
    if (is_admin()) return true;
    
    $permissions = [
        'student' => [
            'view_profile', 'edit_profile',
            'view_own_contract', 'view_own_invoices', 'view_own_payments',
            'create_registration', 'view_own_registrations',
            'create_maintenance', 'view_own_maintenance',
            'create_complaint', 'view_own_complaints',
            'create_visitor', 'view_own_visitors',
            'create_checkout'
        ]
    ];
    
    $role = auth()['role'];
    return in_array($action, $permissions[$role] ?? []);
}

// String functions
function slug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

function truncate($string, $length = 100, $suffix = '...') {
    if (mb_strlen($string) <= $length) return $string;
    return mb_substr($string, 0, $length) . $suffix;
}

// Date functions
function format_date($date, $format = 'd/m/Y') {
    if (!$date) return '';
    return date($format, strtotime($date));
}

function time_ago($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Vừa xong';
    if ($diff < 3600) return floor($diff / 60) . ' phút trước';
    if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
    if ($diff < 2592000) return floor($diff / 86400) . ' ngày trước';
    if ($diff < 31536000) return floor($diff / 2592000) . ' tháng trước';
    return floor($diff / 31536000) . ' năm trước';
}

// Number functions
function format_money($amount) {
    return number_format($amount, 0, ',', '.') . ' ₫';
}

function format_number($number, $decimals = 0) {
    return number_format($number, $decimals, ',', '.');
}

// Security functions
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// Upload functions
function upload_file($file, $path, $allowed = ['jpg', 'jpeg', 'png', 'pdf']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Upload failed'];
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        return ['error' => 'File type not allowed'];
    }
    
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
    
    $filename = uniqid() . '.' . $ext;
    $target = $path . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['error' => 'Failed to save file'];
}

/**
 * Lấy giá trị setting từ database
 */
function setting($key, $default = null) {
    static $settings = null;
    
    // Chỉ load một lần
    if ($settings === null) {
        $db = \App\Core\Database::getInstance();
        $results = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        foreach ($results as $row) {
            $settings[$row->setting_key] = $row->setting_value;
        }
    }
    
    return $settings[$key] ?? $default;
}