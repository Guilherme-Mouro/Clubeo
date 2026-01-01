<?php
function requireAuth($pdo)
{
    // 1. Obter os headers
    $headers = getallheaders();
    $token = null;

    // Verificar se o Header Authorization existe
    if (isset($headers['Authorization'])) {
        // Formato esperado: "Bearer {token}"
        $token = str_replace('Bearer ', '', $headers['Authorization']);
    }

    if (!$token) {
        http_response_code(401);
        echo json_encode(["error" => "Token não fornecido"]);
        exit; // Para a execução aqui
    }

    // 2. Verificar na base de dados
    $stmt = $pdo->prepare("SELECT id FROM users WHERE session_token = :token LIMIT 1");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(401);
        echo json_encode(["error" => "Sessão inválida ou expirada"]);
        exit; // Para a execução aqui
    }

    // Devolve o ID para ser usado no ficheiro principal
    return $user['id'];
}