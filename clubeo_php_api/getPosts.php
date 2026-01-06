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
    $clubId = isset($_GET['id']) ? intval($_GET['id']) : null;

    if (!$clubId) {
        echo json_encode([]);
        exit;
    }

    $sql = "
        SELECT 
            posts.id, 
            posts.content, 
            posts.created_at, 
            posts.likes_num,
            users.username,
            users.image_profile as user_avatar 
        FROM posts
        JOIN users ON posts.user_id = users.id
        WHERE posts.club_id = :clubId
        ORDER BY posts.created_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['clubId' => $clubId]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($posts);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error"]);
}
?>