<?php
return [
    // ==================== PUBLIC ROUTES ====================
    [
        'method' => 'GET',
        'path' => '/',
        'handler' => 'DashboardController@index'
    ],
    [
        'method' => 'GET',
        'path' => '/login',
        'handler' => 'AuthController@showLogin'
    ],
    [
        'method' => 'POST',
        'path' => '/login',
        'handler' => 'AuthController@login'
    ],
    [
        'method' => 'GET',
        'path' => '/register',
        'handler' => 'AuthController@showRegister'
    ],
    [
        'method' => 'POST',
        'path' => '/register',
        'handler' => 'AuthController@register'
    ],
    [
        'method' => 'GET',
        'path' => '/logout',
        'handler' => 'AuthController@logout'
    ],
    
    // ==================== DASHBOARD ====================
['method' => 'GET', 'path' => '/', 'handler' => 'DashboardController@index'],
['method' => 'GET', 'path' => '/dashboard', 'handler' => 'DashboardController@index', 'middlewares' => ['Auth']],
    
    // ==================== STUDENTS ====================
    [
        'method' => 'GET',
        'path' => '/students',
        'handler' => 'StudentController@index',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'GET',
        'path' => '/students/create',
        'handler' => 'StudentController@create',
        'middlewares' => ['Auth', 'Admin']
    ],
    [
        'method' => 'POST',
        'path' => '/students',
        'handler' => 'StudentController@store',
        'middlewares' => ['Auth', 'Admin']
    ],
    [
        'method' => 'GET',
        'path' => '/students/{id}',
        'handler' => 'StudentController@show',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'GET',
        'path' => '/students/{id}/edit',
        'handler' => 'StudentController@edit',
        'middlewares' => ['Auth', 'Admin']
    ],
    [
        'method' => 'POST',
        'path' => '/students/{id}',
        'handler' => 'StudentController@update',
        'middlewares' => ['Auth', 'Admin']
    ],
    [
        'method' => 'GET',
        'path' => '/students/{id}/delete',
        'handler' => 'StudentController@delete',
        'middlewares' => ['Auth', 'Admin']
    ],
    
    // ==================== ROOMS ====================
    [
        'method' => 'GET',
        'path' => '/rooms',
        'handler' => 'RoomController@index',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'GET',
        'path' => '/rooms/floor-plan',
        'handler' => 'RoomController@floorPlan',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'GET',
        'path' => '/rooms/create',
        'handler' => 'RoomController@create',
        'middlewares' => ['Auth', 'Admin']
    ],
    [
        'method' => 'POST',
        'path' => '/rooms',
        'handler' => 'RoomController@store',
        'middlewares' => ['Auth', 'Admin']
    ],
    [
        'method' => 'GET',
        'path' => '/rooms/{id}',
        'handler' => 'RoomController@show',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'GET',
        'path' => '/rooms/{id}/edit',
        'handler' => 'RoomController@edit',
        'middlewares' => ['Auth', 'Admin']
    ],
    [
        'method' => 'POST',
        'path' => '/rooms/{id}',
        'handler' => 'RoomController@update',
        'middlewares' => ['Auth', 'Admin']
    ],
    [
        'method' => 'GET',
        'path' => '/rooms/{id}/delete',
        'handler' => 'RoomController@delete',
        'middlewares' => ['Auth', 'Admin']
    ],
    
    // ==================== REGISTRATIONS (Đơn đăng ký KTX) ====================
    [
        'method' => 'GET',
        'path' => '/registrations',
        'handler' => 'RegistrationController@index',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'GET',
        'path' => '/registrations/create',
        'handler' => 'RegistrationController@create',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'POST',
        'path' => '/registrations',
        'handler' => 'RegistrationController@store',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'GET',
        'path' => '/registrations/{id}',
        'handler' => 'RegistrationController@show',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'GET',
        'path' => '/registrations/{id}/approve',
        'handler' => 'RegistrationController@approve',
        'middlewares' => ['Auth', 'Admin']
    ],
    [
        'method' => 'GET',
        'path' => '/registrations/{id}/assign-room',
        'handler' => 'RegistrationController@assignRoom',
        'middlewares' => ['Auth', 'Admin']
    ],
    [
        'method' => 'POST',
        'path' => '/registrations/{id}/assign-room',
        'handler' => 'RegistrationController@storeAssignment',
        'middlewares' => ['Auth', 'Admin']
    ],
    
    // Contracts
['method' => 'GET', 'path' => '/contracts', 'handler' => 'ContractController@index', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/contracts/{id}', 'handler' => 'ContractController@show', 'middlewares' => ['Auth']],
    
    // ==================== INVOICES ====================
    [
        'method' => 'GET',
        'path' => '/invoices',
        'handler' => 'InvoiceController@index',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'GET',
        'path' => '/invoices/{id}',
        'handler' => 'InvoiceController@show',
        'middlewares' => ['Auth']
    ],
    
    // ==================== PAYMENTS ====================
['method' => 'GET', 'path' => '/payments', 'handler' => 'PaymentController@index', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/payments/create', 'handler' => 'PaymentController@create', 'middlewares' => ['Auth']],
['method' => 'POST', 'path' => '/payments', 'handler' => 'PaymentController@store', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/payments/{id}', 'handler' => 'PaymentController@show', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/payments/{id}/momo', 'handler' => 'PaymentController@momoPayment', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/payments/momo/callback', 'handler' => 'PaymentController@momoCallback'],
['method' => 'POST', 'path' => '/payments/momo/ipn', 'handler' => 'PaymentController@momoIpn'],

// API
['method' => 'GET', 'path' => '/api/payment-status/{id}', 'handler' => 'ApiController@paymentStatus', 'middlewares' => ['Auth']],
    
    // ==================== MAINTENANCE ====================
    // Maintenance routes
['method' => 'GET', 'path' => '/maintenance', 'handler' => 'MaintenanceController@index', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/maintenance/create', 'handler' => 'MaintenanceController@create', 'middlewares' => ['Auth']],
['method' => 'POST', 'path' => '/maintenance', 'handler' => 'MaintenanceController@store', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/maintenance/{id}', 'handler' => 'MaintenanceController@show', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/maintenance/{id}/process', 'handler' => 'MaintenanceController@process', 'middlewares' => ['Auth', 'Admin']],
['method' => 'GET', 'path' => '/maintenance/{id}/complete', 'handler' => 'MaintenanceController@complete', 'middlewares' => ['Auth', 'Admin']],

    // ==================== SERVICE REGISTRATIONS ====================
    ['method' => 'GET', 'path' => '/services', 'handler' => 'ServiceController@index', 'middlewares' => ['Auth']],
    ['method' => 'GET', 'path' => '/services/create', 'handler' => 'ServiceController@create', 'middlewares' => ['Auth']],
    ['method' => 'POST', 'path' => '/services', 'handler' => 'ServiceController@store', 'middlewares' => ['Auth']],
    ['method' => 'GET', 'path' => '/services/{id}', 'handler' => 'ServiceController@show', 'middlewares' => ['Auth']],
    ['method' => 'POST', 'path' => '/services/{id}/process', 'handler' => 'ServiceController@process', 'middlewares' => ['Auth', 'Admin']],
    ['method' => 'POST', 'path' => '/services/{id}/complete', 'handler' => 'ServiceController@complete', 'middlewares' => ['Auth', 'Admin']],
    
    // ==================== NOTICES ====================
    [
        'method' => 'GET',
        'path' => '/notices',
        'handler' => 'NoticeController@index',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'GET',
        'path' => '/notices/create',
        'handler' => 'NoticeController@create',
        'middlewares' => ['Auth', 'Admin']
    ],
    [
        'method' => 'POST',
        'path' => '/notices',
        'handler' => 'NoticeController@store',
        'middlewares' => ['Auth', 'Admin']
    ],
    
    // Complaints
['method' => 'GET', 'path' => '/complaints', 'handler' => 'ComplaintController@index', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/complaints/create', 'handler' => 'ComplaintController@create', 'middlewares' => ['Auth']],
['method' => 'POST', 'path' => '/complaints', 'handler' => 'ComplaintController@store', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/complaints/{id}', 'handler' => 'ComplaintController@show', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/complaints/{id}/respond', 'handler' => 'ComplaintController@respond', 'middlewares' => ['Auth', 'Admin']],
['method' => 'POST', 'path' => '/complaints/{id}/respond', 'handler' => 'ComplaintController@storeResponse', 'middlewares' => ['Auth', 'Admin']],
    
    // ==================== VISITORS ====================
    [
        'method' => 'GET',
        'path' => '/visitors',
        'handler' => 'VisitorController@index',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'GET',
        'path' => '/visitors/create',
        'handler' => 'VisitorController@create',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'POST',
        'path' => '/visitors',
        'handler' => 'VisitorController@store',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'GET',
        'path' => '/visitors/{id}/checkout',
        'handler' => 'VisitorController@checkout',
        'middlewares' => ['Auth']
    ],
    
    // ==================== CHECK-OUT ====================
[
    'method' => 'GET',
    'path' => '/check-out',
    'handler' => 'CheckOutController@index',
    'middlewares' => ['Auth']
],
[
    'method' => 'GET',
    'path' => '/check-out/create',
    'handler' => 'CheckOutController@create',
    'middlewares' => ['Auth']
],
[
    'method' => 'POST',
    'path' => '/check-out',
    'handler' => 'CheckOutController@store',
    'middlewares' => ['Auth']
],
[
    'method' => 'POST',
    'path' => '/check-out/approve',
    'handler' => 'CheckOutController@approve',
    'middlewares' => ['Auth', 'Admin']
],
[
    'method' => 'POST',
    'path' => '/check-out/complete',
    'handler' => 'CheckOutController@complete',
    'middlewares' => ['Auth', 'Admin']
],
    
    // ==================== PROFILE ====================
    [
        'method' => 'GET',
        'path' => '/profile',
        'handler' => 'StudentController@profile',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'POST',
        'path' => '/profile',
        'handler' => 'StudentController@updateProfile',
        'middlewares' => ['Auth']
    ],
    
   // Reports
['method' => 'GET', 'path' => '/reports', 'handler' => 'ReportController@index', 'middlewares' => ['Auth', 'Admin']],
['method' => 'GET', 'path' => '/reports/financial', 'handler' => 'ReportController@financial', 'middlewares' => ['Auth', 'Admin']],
['method' => 'GET', 'path' => '/reports/occupancy', 'handler' => 'ReportController@occupancy', 'middlewares' => ['Auth', 'Admin']],
['method' => 'GET', 'path' => '/reports/export', 'handler' => 'ReportController@export', 'middlewares' => ['Auth', 'Admin']],
    
    // ==================== SETTINGS ====================
    [
        'method' => 'GET',
        'path' => '/settings',
        'handler' => 'SettingController@index',
        'middlewares' => ['Auth', 'Admin']
    ],
    [
        'method' => 'POST',
        'path' => '/settings',
        'handler' => 'SettingController@update',
        'middlewares' => ['Auth', 'Admin']
    ],
    
    // ==================== API ROUTES ====================
    [
        'method' => 'GET',
        'path' => '/api/rooms-by-building',
        'handler' => 'ApiController@roomsByBuilding',
        'middlewares' => ['Auth']
    ],
    [
        'method' => 'GET',
        'path' => '/api/available-beds',
        'handler' => 'ApiController@availableBeds',
        'middlewares' => ['Auth']
    ],

    ['method' => 'GET', 'path' => '/api/payment-status/{id}', 'handler' => 'ApiController@paymentStatus', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/payments/momo/callback', 'handler' => 'PaymentController@momoCallback'],
['method' => 'POST', 'path' => '/payments/momo/ipn', 'handler' => 'PaymentController@momoIpn'],


[
    'method' => 'GET',
    'path' => '/notices/{id}/delete',
    'handler' => 'NoticeController@delete',
    'middlewares' => ['Auth', 'Admin']
],

// Rules
['method' => 'GET', 'path' => '/rules', 'handler' => 'RuleController@index', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/rules/create', 'handler' => 'RuleController@create', 'middlewares' => ['Auth', 'Admin']],
['method' => 'POST', 'path' => '/rules', 'handler' => 'RuleController@store', 'middlewares' => ['Auth', 'Admin']],
['method' => 'GET', 'path' => '/rules/{id}/edit', 'handler' => 'RuleController@edit', 'middlewares' => ['Auth', 'Admin']],
['method' => 'POST', 'path' => '/rules/{id}', 'handler' => 'RuleController@update', 'middlewares' => ['Auth', 'Admin']],
['method' => 'GET', 'path' => '/rules/{id}/delete', 'handler' => 'RuleController@delete', 'middlewares' => ['Auth', 'Admin']],

// Violations
['method' => 'GET', 'path' => '/violations', 'handler' => 'ViolationController@index', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/violations/create', 'handler' => 'ViolationController@create', 'middlewares' => ['Auth', 'Admin']],
['method' => 'POST', 'path' => '/violations', 'handler' => 'ViolationController@store', 'middlewares' => ['Auth', 'Admin']],
['method' => 'GET', 'path' => '/violations/{id}', 'handler' => 'ViolationController@show', 'middlewares' => ['Auth']],
['method' => 'GET', 'path' => '/violations/{id}/process', 'handler' => 'ViolationController@process', 'middlewares' => ['Auth', 'Admin']],

];