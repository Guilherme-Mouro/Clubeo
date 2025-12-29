<?php
// CORS and Security Headers
$allowed_origin = "https://guimou.antrob.eu/login";
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Security hardening headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Method not allowed", 405);
    }

    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (empty($data['email']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "Email and password are required"]);
        exit;
    }

    $email = $data['email'];
    $password = $data['password'];

    // Fetch user by email
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Generic error message prevents user enumeration
    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid email or password"]);
        exit;
    }

    // Generate a secure random session token
    $token = bin2hex(random_bytes(32));

    // Store the token in the database associated with the user
    // This allows you to verify the user in other pages using the token instead of just the ID
    $updateStmt = $pdo->prepare("UPDATE users SET session_token = :token WHERE id = :id");
    $updateStmt->execute([
        'token' => $token,
        'id' => $user['id']
    ]);

    // Success response returning both ID and the secure Token
    echo json_encode([
        "message" => "Login successful",
        "userId" => $user['id'],
        "token" => $token
    ]);

} catch (Exception $e) {
    // Log error internally and send a generic message to the client
    error_log($e->getMessage());
    $code = ($e->getCode() >= 400 && $e->getCode() <= 500) ? $e->getCode() : 500;
    http_response_code($code);
    echo json_encode(["error" => "Internal server error"]);
}
?>