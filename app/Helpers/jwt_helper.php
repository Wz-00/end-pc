<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateJWT($email) {
    $key = getenv('JWT_SECRET');
    $payload = [
        'iss' => 'yourdomain.com',
        'aud' => 'yourdomain.com',
        'iat' => time(),
        'exp' => time() + 60 * 60 * 24 * 30,
        'email' => $email
    ];
    return JWT::encode($payload, $key, 'HS256');
}

function verifyJWT($token) {
    return JWT::decode($token, new Key(getenv('JWT_SECRET'), 'HS256'));
}
?>
