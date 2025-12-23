<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require 'db.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id']) && isset($data['status'])) {
        $id = $data['id'];
        $status = $data['status'];

        $stmt = $pdo->prepare("UPDATE users SET online = :status WHERE id = :id");
        $stmt->execute([$status, $id]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
?>