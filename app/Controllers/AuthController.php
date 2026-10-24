<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Student;

class AuthController extends Controller {
    
    public function showLogin() {
        if (is_logged_in()) {
            return redirect('/dashboard');
        }
        
        $data = [
            'title' => 'Đăng nhập - ' . setting('dormitory_name', 'DormFlow')
        ];
        
        $this->view('auth/login', $data);
    }
    
    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            flash('error', 'Vui lòng nhập đầy đủ thông tin');
            return redirect('/login');
        }
        
        // Admin mặc định
        if ($email === 'admin@ktx.edu.vn' && $password === 'password') {
            $_SESSION['user'] = [
                'id' => 1,
                'username' => 'admin',
                'email' => 'admin@ktx.edu.vn',
                'full_name' => 'Administrator',
                'role' => 'admin',
                'avatar' => null
            ];
            return redirect('/dashboard');
        }
        
        // Kiểm tra user trong database
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        if (!$user || !$userModel->verifyPassword($password, $user->password)) {
            flash('error', 'Email hoặc mật khẩu không đúng');
            return redirect('/login');
        }
        
        $_SESSION['user'] = [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'full_name' => $user->full_name,
            'role' => $user->role,
            'avatar' => $user->avatar
        ];
        
        if ($user->role === 'admin') {
            return redirect('/dashboard');
        } else {
            return redirect('/dashboard');
        }
    }
    
    public function showRegister() {
        if (is_logged_in()) {
            return redirect('/dashboard');
        }
        
        $data = [
            'title' => 'Đăng ký - ' . setting('dormitory_name', 'DormFlow')
        ];
        
        $this->view('auth/register', $data);
    }
    
    public function register() {
        // Validate dữ liệu
        $errors = [];
        
        if (empty($_POST['full_name'])) {
            $errors['full_name'] = 'Vui lòng nhập họ tên';
        }
        if (empty($_POST['email'])) {
            $errors['email'] = 'Vui lòng nhập email';
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }
        if (empty($_POST['phone'])) {
            $errors['phone'] = 'Vui lòng nhập số điện thoại';
        }
        if (empty($_POST['password'])) {
            $errors['password'] = 'Vui lòng nhập mật khẩu';
        } elseif (strlen($_POST['password']) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }
        if ($_POST['password'] !== $_POST['password_confirmation']) {
            $errors['password_confirmation'] = 'Xác nhận mật khẩu không khớp';
        }
        
        if (!empty($errors)) {
            flash('error', 'Vui lòng kiểm tra lại thông tin');
            flash('errors', $errors);
            flash('old', $_POST);
            return redirect('/register');
        }
        
        // Kiểm tra email đã tồn tại
        $userModel = new User();
        $existingUser = $userModel->findByEmail($_POST['email']);
        
        if ($existingUser) {
            flash('error', 'Email đã được sử dụng');
            flash('old', $_POST);
            return redirect('/register');
        }
        
        // Tạo user mới
        $username = explode('@', $_POST['email'])[0] . rand(100, 999);
        
        $userId = $userModel->create([
            'username' => $username,
            'email' => $_POST['email'],
            'password' => $userModel->hashPassword($_POST['password']),
            'full_name' => $_POST['full_name'],
            'phone' => $_POST['phone'],
            'role' => 'student',
            'status' => 'active'
        ]);
        
        if (!$userId) {
            flash('error', 'Đăng ký thất bại, vui lòng thử lại');
            return redirect('/register');
        }
        
        // Tạo student profile
        $studentModel = new Student();
        $studentModel->create([
            'user_id' => $userId,
            'full_name' => $_POST['full_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone']
        ]);
        
        flash('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
        return redirect('/login');
    }
    
    public function logout() {
        session_destroy();
        return redirect('/login');
    }
}