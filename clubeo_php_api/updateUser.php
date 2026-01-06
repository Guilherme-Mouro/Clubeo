<?php
// CORS and Security Headers
$allowed_origin = "https://guimou.antrob.eu";
header("Access-Control-Allow-Origin: $allowed_origin");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Security hardening headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require 'db.php';
require 'authToken.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Method not allowed", 405);
    }

    $userId = requireAuth($pdo);

    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    $username = $data['username'] ?? '';
    $description = $data['description'] ?? '';
    $email = $data['email'] ?? '';
    $avatarUrl = $data['avatar_url'] ?? '';
    $password = $data['password'] ?? '';

    $pdo->beginTransaction();

    $sql = "UPDATE users SET 
            username = :username, 
            description = :description, 
            email = :email, 
            avatar_url = :avatar";
    
    $params = [
        'username' => $username,
        'description' => $description,
        'email' => $email,
        'avatar' => $avatarUrl,
        'userId' => $userId
    ];

    if (!empty($password)) {
        $sql .= ", password = :password";
        $params['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $sql .= " WHERE id = :userId";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $pdo->commit();

    http_response_code(200);
    echo json_encode(["message" => "Profile updated!"]);

} catch (Exception $e) { 
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $code = ($e->getCode() >= 400 && $e->getCode() <= 500) ? $e->getCode() : 500;
    http_response_code($code);
    echo json_encode(["error" => $e->getMessage()]);
}
?>