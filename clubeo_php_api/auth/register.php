<?php
// CORS and Security Headers
$allowed_origin = "https://guimou.antrob.eu/register";
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

    if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    $username = $data['username'];
    $email = $data['email'];

    // Check if username or email is already registered
    $stmtCheck = $pdo->prepare("SELECT username, email FROM users WHERE username = :username OR email = :email");
    $stmtCheck->execute(['username' => $username, 'email' => $email]);
    $existingUsers = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);

    if (count($existingUsers) > 0) {
        $usernameTaken = false;
        $emailTaken = false;

        foreach ($existingUsers as $user) {
            if ($user['username'] === $username) $usernameTaken = true;
            if ($user['email'] === $email) $emailTaken = true;
        }

        http_response_code(409);
        echo json_encode([
            "usernameTaken" => $usernameTaken,
            "emailTaken" => $emailTaken,
            "error" => "Conflict: User already exists"
        ]);
        exit;
    }

    // Hash password only after confirming the user is unique (saves CPU)
    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

    // Generate a secure random session token for the new user
    $token = bin2hex(random_bytes(32));

    $stmtInsert = $pdo->prepare("INSERT INTO users (username, email, password, session_token) VALUES (:username, :email, :password, :token)");
    $stmtInsert->execute([
        'username' => $username,
        'email' => $email,
        'password' => $hashedPassword,
        'token' => $token
    ]);

    http_response_code(201);
    echo json_encode([
        "message" => "Account created successfully",
        "userId" => $pdo->lastInsertId(),
        "token" => $token    
    ]);

} catch (Exception $e) {
    // Log error internally and send a generic message to the client
    error_log($e->getMessage());
    $code = ($e->getCode() >= 400 && $e->getCode() <= 500) ? $e->getCode() : 500;
    http_response_code($code);
    echo json_encode(["error" => "Internal server error"]);
}