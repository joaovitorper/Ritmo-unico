<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/conexao.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Método não permitido.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$nome = trim((string) ($_POST['nome'] ?? ''));
$email = trim(strtolower((string) ($_POST['email'] ?? '')));
$usuario_id = (int) $_SESSION['usuario_id'];

if ($nome === '' || strlen($nome) < 3 || !preg_match('/\s/', $nome)) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Digite seu nome completo.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Digite um e-mail válido.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $conexao->prepare('SELECT id FROM usuarios WHERE email = ? AND id != ? LIMIT 1');
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao validar dados do usuário.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param('si', $email, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt->close();
    http_response_code(409);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Este e-mail já está cadastrado para outro usuário.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->close();

$stmt = $conexao->prepare('UPDATE usuarios SET nome = ?, email = ? WHERE id = ?');
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao preparar atualização do perfil.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param('ssi', $nome, $email, $usuario_id);

if (!$stmt->execute()) {
    $stmt->close();
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Não foi possível salvar o perfil.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->close();

$_SESSION['usuario_nome'] = $nome;
$_SESSION['usuario_email'] = $email;

echo json_encode([
    'sucesso' => true,
    'mensagem' => 'Perfil salvo com sucesso!'
], JSON_UNESCAPED_UNICODE);
