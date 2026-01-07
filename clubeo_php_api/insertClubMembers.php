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

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require 'db.php';
require 'authToken.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Method not allowed", 405);
    }

    $userId = requireAuth($pdo);

    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (!isset($data['clubId'])) {
        throw new Exception("Club ID required");
    }

    $clubId = $data['clubId'];

    $pdo->beginTransaction();

    $checkSql = "SELECT id FROM club_members WHERE club_id = :clubId AND user_id = :userId";
    $stmtCheck = $pdo->prepare($checkSql);
    $stmtCheck->execute(['clubId' => $clubId, 'userId' => $userId]);

    if ($stmtCheck->rowCount() > 0) {
        $pdo->rollBack();
        echo json_encode(["message" => "Already a member"]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO club_members (club_id, user_id, role) VALUES (:clubId, :userId, :role)");
    $stmt->execute([
        'clubId' => $clubId,
        'userId' => $userId,
        'role'   => 'member'
    ]);

    $stmt = $pdo->prepare("UPDATE clubs SET members_num = members_num + 1 WHERE id = :clubId");
    $stmt->execute(['clubId' => $clubId]);

    $pdo->commit();

    http_response_code(200);
    echo json_encode(["message" => "Joined club successfully"]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>