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

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require 'db.php';

try {
    // Only POST requests are allowed
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Method not allowed", 405);
    }

    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    // Security validation: ID, Token, and Status are mandatory
    if (!isset($data['id']) || !isset($data['token']) || !isset($data['status'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required parameters"]);
        exit;
    }

    $id = $data['id'];
    $token = $data['token'];
    $status = (int)$data['status']; // Force cast to integer (0 or 1)

    // The query verifies both ID and TOKEN simultaneously. 
    // If the token is incorrect or does not match the ID, no rows will be updated.
    $stmt = $pdo->prepare("
        UPDATE users 
        SET online = :status, 
            last_seen = NOW() 
        WHERE id = :id AND session_token = :token
    ");
    
    $stmt->execute([
        'status' => $status,
        'id'     => $id,
        'token'  => $token
    ]);

    // Check if any row was actually affected by the query
    if ($stmt->rowCount() > 0) {
        echo json_encode(["message" => "Status updated successfully", "status" => $status]);
    } else {
        // If rowCount is 0, either the token is invalid or the status was already set to that value
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized or no changes made"]);
    }

} catch (Exception $e) {
    // Log the error message internally for debugging
    error_log("UpdateStatus Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode(["error" => "Internal server error"]);
}
?>