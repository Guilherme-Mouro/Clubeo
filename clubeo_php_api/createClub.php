<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require 'db.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['name'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    $name = $data['name'];
    $description = $data['description'];
    $adminId = $data['adminId'];

    $stmt = $pdo->prepare("INSERT INTO clubs (name, description, admin_id) VALUES (:name, :description, :adminId)");
    
    $stmt->execute([
        'name' => $name,
        'description' => $description,
        'adminId' => $adminId
    ]);

    http_response_code(201);
    echo json_encode(["message" => "Club created successfully"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
?>