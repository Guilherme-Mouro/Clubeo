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