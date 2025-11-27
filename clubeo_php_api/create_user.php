<?php
header("Content-Type: application/json");
require 'db.php';

// Receber dados JSON do Nuxt
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name']) || !isset($data['email'])) {
    http_response_code(400);
    echo json_encode(["error" => "Name and email are required"]);
    exit;
}

$name = $data['name'];
$email = $data['email'];

try {
    $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
    $stmt->execute(['name' => $name, 'email' => $email]);

    echo json_encode([
        "id" => $pdo->lastInsertId(),
        "name" => $name,
        "email" => $email
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
