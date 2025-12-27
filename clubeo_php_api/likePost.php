<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require 'db.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['userId']) || !isset($data['postId'])) {
        throw new Exception("Dados inválidos");
    }

    $userId = (int)$data['userId'];
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