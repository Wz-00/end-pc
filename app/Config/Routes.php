<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Login and Registration Routes
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function (RouteCollection $routes) {
    // $routes->get('users', 'Auth::getUsers'); // Get all users
    // $routes->get('users/otp', 'Auth::getOtps'); // Get all OTPs

    // OTP terkait registrasi
    $routes->post('users/sendOtp', 'Auth::sendOtp');              // Send otp for register
    $routes->post('users/otp/verify', 'Auth::verifyOtp');     // Otp verification for register

    // Registrasi user
    $routes->post('users/register', 'Auth::register');                 // Register new user

    // Login
    $routes->post('auth/login', 'Auth::login');               // Login and generate JWT

    // Lupa password (simulasi email OTP)
    $routes->post('auth/password/forgot', 'Auth::forgotPassword');       // Kirim OTP lupa password
    $routes->post('auth/password/verify', 'Auth::verifyForgotOtp');      // Verifikasi OTP lupa password
    $routes->put('auth/password/reset', 'Auth::resetPassword');          // Reset password

    // delete user
    $routes->delete('users/delete', 'Auth::deleteUser');
});

// category routes
$routes->group('api/category', ['namespace' => 'App\Controllers\Api', 'filter' => 'adminonly'], function (RouteCollection $routes) {
    $routes->post('create', 'Product::createCategory'); // Create a new category
    $routes->put('update', 'Product::updateCategory'); // Update a category 
    $routes->delete('delete/(:segment)', 'Product::deleteCategory/$1'); // Delete a category by ID
    $routes->get('index', 'Product::index'); // Get all categories
});

// product routes
$routes->group('api/product', ['namespace' => 'App\Controllers\Api', 'filter' => 'adminonly'], function (RouteCollection $routes) {
    $routes->post('create', 'Product::createProduct'); // Create a new product
    $routes->put('update', 'Product::updateProduct'); // Update a product
    $routes->delete('delete/(:segment)', 'Product::deleteProduct/$1'); // Delete a product by ID
});

