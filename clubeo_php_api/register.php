<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require 'db.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    $username = $data['username'];
    $email = $data['email'];

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
            "error" => "User or Email already exists"
        ]);
        exit;
    }

    $password = password_hash($data['password'], PASSWORD_BCRYPT);

    $stmtInsert = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
    $stmtInsert->execute([
        'username' => $username,
        'email' => $email,
        'password' => $password
    ]);

    $userId = $pdo->lastInsertId();

    http_response_code(201);
    echo json_encode([
        "message" => "Account created successfully",
        "userId"=> $userId
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}