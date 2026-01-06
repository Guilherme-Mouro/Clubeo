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