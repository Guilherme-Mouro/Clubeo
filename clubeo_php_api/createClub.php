<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require 'db.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    $name = $data['name'];
    $description = $data['description'];
    $adminId = $data['adminId'];

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO clubs (name, description, admin_id, members_num) VALUES (:name, :description, :adminId, 1)");
    $stmt->execute([
        'name' => $name,
        'description' => $description,
        'adminId' => $adminId
    ]);

    $clubId = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO club_members (club_id, user_id, role) VALUES (:clubId, :userId, 'admin')");
    $stmt->execute([
        'clubId' => $clubId,
        'userId' => $adminId,
    ]);

    $pdo->commit();

    http_response_code(201);
    echo json_encode(["message" => "Club created and admin assigned", "clubId" => $clubId]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>