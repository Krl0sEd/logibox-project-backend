<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

header("Content-Type: application/json; charset=UTF-8");

// ======================= CONEXÃO BD ============================
$host = "db";
$user = "CJJPW";
$password = "CJJPW";
$dbname = "Estoque";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Falha na conexão: " . $conn->connect_error]));
}

// ======================= RECEBER POST ============================
$data = json_decode(file_get_contents("php://input"), true);

$login = $data["login"] ?? null;
$campo = $data["campo"] ?? null;
$resposta = $data["resposta"] ?? null;

if (!$login || !$campo || !$resposta) {
    echo json_encode(["error" => "Dados incompletos."]);
    exit;
}

// ======================= BUSCAR USUÁRIO ============================
$sql = "SELECT $campo FROM Usuario WHERE login = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $login);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["error" => "Usuário não encontrado."]);
    exit;
}

$user = $res->fetch_assoc();

// ======================= VALIDAR RESPOSTA ============================
$correto = strtolower(trim($user[$campo]));
$enviado = strtolower(trim($resposta));

if ($correto === $enviado) {
    echo json_encode(["status" => "aprovado"]);
} else {
    echo json_encode([
        "status" => "negado",
        "error" => "Resposta incorreta."
    ]);
}

$stmt->close();
$conn->close();
?>