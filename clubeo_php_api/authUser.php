<?php
header("Content-Type: application/json");
require 'db.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$action = $_GET['action'] ?? null;

switch ($action) {
    case "register":
        createUser();
        break;

    case "login":
        loginUser();
        break;

    default:
        echo json_encode(["error" => "Invalid API action"]);
        break;
}

function createUser()
{
    global $pdo;

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['username'], $data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "Fields are missing"]);
        exit;
    }

    $username = $data['username'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password) 
            VALUES (:username, :email, :password)
        ");

        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $password
        ]);

        echo json_encode([
            "id" => $pdo->lastInsertId(),
            "username" => $username,
            "email" => $email
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

function loginUser()
{
    global $pdo;

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "Email and password required"]);
        exit;
    }

    $email = $data['email'];
    $password = $data['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid credentials"]);
        exit;
    }

    echo json_encode([
        "message" => "Login successful",
        "user" => [
            "id" => $user['id'],
            "username" => $user['username'],
            "email" => $user['email']
        ]
    ]);
}
