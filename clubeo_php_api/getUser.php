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

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require 'db.php';


try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['id']) || empty(trim($data['token']))) {
        http_response_code(400);
        echo json_encode(["error" => "User ID and Token is required"]);
        exit;
    }

    $id = $data['id'];
    $token = $data['token'];

    $stmt = $pdo->prepare("SELECT id, username, email, password, description, avatar_url FROM users WHERE id = :id AND session_token = :token LIMIT 1");
    $stmt->execute([
        'id' => $id,
        'token'=> $token
    ]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["error" => "User not found"]);
        exit;
    }

    echo json_encode(["user" => $user]);

} catch (Exception $e) {
    // Log error internally and send a generic message to the client
    error_log($e->getMessage());
    $code = ($e->getCode() >= 400 && $e->getCode() <= 500) ? $e->getCode() : 500;
    http_response_code($code);
    echo json_encode(["error" => "Internal server error"]);
}
?>