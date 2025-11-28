<?php
date_default_timezone_set('America/Sao_Paulo');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit;
}

header("Content-Type: application/json; charset=UTF-8");

$host = "db";
$user = "CJJPW";
$password = "CJJPW";
$dbname = "Estoque";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "Falha na conexão: " . $conn->connect_error]));
}

$method = $_SERVER['REQUEST_METHOD'];

// =========================================
// GET
// =========================================
if ($method === 'GET') {

    $login = $_GET['login'] ?? null;

    if (!$login) {
        http_response_code(400);
        die(json_encode(["error" => "Envie ?login=seuLogin"]));
    }

    $sql = "SELECT id_usuario, cpf, nome, email, login
            FROM Usuario WHERE login = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        die(json_encode(["error" => "Erro ao preparar query: " . $conn->error]));
    }

    $stmt->bind_param("s", $login);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Usuário não encontrado."]);
    } else {
        echo json_encode($res->fetch_assoc());
    }

    $stmt->close();
    $conn->close();
    exit;
}

// =========================================
// POST - LOGIN
// =========================================
if ($method === 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    $login = $data["login"] ?? null;
    $senha = $data["senha"] ?? null;

    if (!$login || !$senha) {
        http_response_code(400);
        die(json_encode(["error" => "Login e senha são obrigatórios."]));
    }

    $sql = "SELECT id_usuario, cpf, nome, email, login, senha, admin, cep, nome_materno, data_nascimento
            FROM Usuario WHERE login = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        die(json_encode(["error" => "Erro ao preparar query: " . $conn->error]));
    }

    $stmt->bind_param("s", $login);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        http_response_code(401);
        die(json_encode(["error" => "Login ou senha inválidos."]));
    }

    $user = $res->fetch_assoc();

    // verifica senha
    if (!password_verify($senha, $user["senha"])) {
        http_response_code(401);
        die(json_encode(["error" => "Login ou senha inválidos."]));
    }

    // registrar login
    $id_usuario = $user["id_usuario"];
    $tipo_2fa = 1;

    $sqlLog = "INSERT INTO Log (data_hora_login, tipo_2fa, id_usuario)
               VALUES (NOW(), ?, ?)";

    $stmtLog = $conn->prepare($sqlLog);
    $stmtLog->bind_param("ii", $tipo_2fa, $id_usuario);
    $stmtLog->execute();
    $stmtLog->close();

    unset($user["senha"]);

    echo json_encode([
        "sucesso" => "Login efetuado com sucesso!",
        "usuario" => $user
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

// Método inválido
http_response_code(405);
echo json_encode(["error" => "Método não suportado."]);

?>