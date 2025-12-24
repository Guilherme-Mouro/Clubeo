<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require 'db.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["userId"])) {
        echo json_encode(["error" => "userId não fornecido"]);
        exit;
    }

    $userId = $data["userId"];
    
    $sql = "SELECT c.id, c.name, c.description, c.image_banner 
        FROM clubs c
        INNER JOIN club_members cm ON c.id = cm.club_id
        WHERE cm.user_id = :userId";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId]);

    $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($clubs);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
?>