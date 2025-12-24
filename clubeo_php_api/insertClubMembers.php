<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require 'db.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    $clubId = $data['clubId'];
    $userId = $data['userId'];

    $pdo->beginTransaction();

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

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>