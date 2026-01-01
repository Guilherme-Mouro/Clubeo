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

    if (empty($data['clubId']) || empty($data['content'])) {
        throw new Exception("Club ID and content are required", 400);
    }

    $clubId = $data['clubId'];
    $content = $data['content'];

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO posts (club_id, user_id, content) VALUES (:clubId, :userId, :content)");
    $stmt->execute([
        'clubId' => $clubId,
        'userId' => $userId,
        'content' => $content
    ]);


    $pdo->commit();

    http_response_code(201);
    echo json_encode(["message" => "Post added", "clubId" => $clubId]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>