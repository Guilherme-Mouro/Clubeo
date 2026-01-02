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
// Required file to verify user token
require 'authToken.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Method not allowed", 405);
    }

    $userId = requireAuth($pdo);

    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (empty($data['name'])) {
        throw new Exception("Club name is required", 400);
    }

    $name = $data['name'];
    $description = $data['description'] ?? '';
    $imageUrl = $data['imageUrl'] ?? '';

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO clubs (name, description, image_banner, admin_id, members_num) VALUES (:name, :description, :imageUrl, :adminId, 1)");
    $stmt->execute([
        'name' => $name,
        'description' => $description,
        'imageUrl' => $imageUrl,
        'adminId' => $userId
    ]);

    $clubId = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO club_members (club_id, user_id, role) VALUES (:clubId, :userId, 'admin')");
    $stmt->execute([
        'clubId' => $clubId,
        'userId' => $userId,
    ]);

    $pdo->commit();

    http_response_code(201);
    echo json_encode([
        "message" => "Club created and admin assigned", 
        "clubId" => $clubId
    ]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    $code = ($e->getCode() >= 400 && $e->getCode() <= 500) ? $e->getCode() : 500;
    http_response_code($code);
    echo json_encode(["error" => $e->getMessage()]);
}
?>