<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require 'db.php';

try {
    $id = isset($_GET['id']) ? intval($_GET['id']) :null;

    if (!$id) {
        echo json_encode(["error" => "ID not found"]);
        exit;
    }

    $stmt = $pdo->query("SELECT * FROM clubs WHERE id = :id");
    $stmt->execute(["id"=> $id]);
    $club = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($club);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
?>