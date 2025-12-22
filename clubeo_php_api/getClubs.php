<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require 'db.php';

try {

    $stmt = $pdo->query("SELECT * FROM clubs ORDER BY created_at DESC");
    $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($clubs);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
?>