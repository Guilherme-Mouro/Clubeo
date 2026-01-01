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
require 'authToken.php'; // O teu ficheiro de verificação

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Method not allowed", 405);
    }

    // 1. VERIFICAÇÃO DE SEGURANÇA
    // A função requireAuth verifica o token no Header e devolve o ID do utilizador
    // Se o token for inválido, a função mata o script aqui com erro 401.
    $userId = requireAuth($pdo);

    // 2. OBTER DADOS DO CORPO DA REQUISIÇÃO (JSON)
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (empty($data['name'])) {
        throw new Exception("O nome do clube é obrigatório", 400);
    }

    $name = $data['name'];
    $description = $data['description'] ?? ''; // Caso não enviem descrição

    // 3. OPERAÇÃO NA BASE DE DADOS
    $pdo->beginTransaction();

    // Inserir o novo clube usando o $userId validado pelo token
    $stmt = $pdo->prepare("INSERT INTO clubs (name, description, admin_id, members_num) VALUES (:name, :description, :adminId, 1)");
    $stmt->execute([
        'name' => $name,
        'description' => $description,
        'adminId' => $userId
    ]);

    $clubId = $pdo->lastInsertId();

    // Inserir o criador como o primeiro membro (admin)
    $stmt = $pdo->prepare("INSERT INTO club_members (club_id, user_id, role) VALUES (:clubId, :userId, 'admin')");
    $stmt->execute([
        'clubId' => $clubId,
        'userId' => $userId,
    ]);

    $pdo->commit();

    // 4. RESPOSTA DE SUCESSO
    http_response_code(201);
    echo json_encode([
        "message" => "Club created and admin assigned", 
        "clubId" => $clubId
    ]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Define o código de erro (se for 401 do auth, mantém 401)
    $code = ($e->getCode() >= 400 && $e->getCode() <= 500) ? $e->getCode() : 500;
    http_response_code($code);
    echo json_encode(["error" => $e->getMessage()]);
}
?>