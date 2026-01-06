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

    if (!isset($data['postId'])) {
        throw new Exception("Dados inválidos");
    }

    $postId = (int)$data['postId'];

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT id FROM user_likes WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$userId, $postId]);
    $likeExistente = $stmt->fetch();

    if ($likeExistente) {
        $stmt = $pdo->prepare("DELETE FROM user_likes WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$userId, $postId]);

        $stmt = $pdo->prepare("UPDATE posts SET likes_num = GREATEST(0, likes_num - 1) WHERE id = ?");
        $stmt->execute([$postId]);

        $status = 'unliked';
    } else {
        $stmt = $pdo->prepare("INSERT INTO user_likes (user_id, post_id) VALUES (?, ?)");
        $stmt->execute([$userId, $postId]);

        $stmt = $pdo->prepare("UPDATE posts SET likes_num = likes_num + 1 WHERE id = ?");
        $stmt->execute([$postId]);

        $status = 'liked';
    }

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'action' => $status
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}