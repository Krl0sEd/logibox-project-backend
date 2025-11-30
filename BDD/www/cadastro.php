<?php
date_default_timezone_set('America/Sao_Paulo');

// ----- INÍCIO DO CÓDIGO DE DEBUG -----
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ----- FIM DO CÓDIGO DE DEBUG -----


// ----- INÍCIO DO CÓDIGO DE PERMISSÃO (CORS) -----
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit;
}
// ----- FIM DO CÓDIGO DE PERMISSÃO (CORS) -----

header ("Content-Type: application/json; charset=UTF-8");

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

if ($method === 'GET') {
    $cpf = isset($_GET['cpf']) ? $_GET['cpf'] : null;
    $email = isset($_GET['email']) ? $_GET['email'] : null;

    if ($cpf) {
        $sql = "SELECT cpf, nome, data_nascimento, sexo, nome_materno, email, telefone_celular, cep, rua, numero_rua, complemento, bairro, estado, cidade, login FROM Usuario WHERE cpf = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            die(json_encode(["error" => "Erro na preparação da query: " . $conn->error]));
        }
        $stmt->bind_param("s", $cpf);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["error" => "Usuário não encontrado."]);
        } else {
            $user = $res->fetch_assoc();
            echo json_encode($user);
        }
        $stmt->close();
        $conn->close();
        exit;
    }

    if ($email) {
        $sql = "SELECT cpf, nome, data_nascimento, sexo, nome_materno, email, telefone_celular, cep, rua, numero_rua, complemento, bairro, estado, cidade, login FROM Usuario WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            die(json_encode(["error" => "Erro na preparação da query: " . $conn->error]));
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["error" => "Usuário não encontrado."]);
        } else {
            $user = $res->fetch_assoc();
            echo json_encode($user);
        }
        $stmt->close();
        $conn->close();
        exit;
    }

    $sql = "SELECT cpf, nome, data_nascimento, sexo, nome_materno, email, telefone_celular, cep, rua, numero_rua, complemento, bairro, estado, cidade, login FROM Usuario";
    $result = $conn->query($sql);
    if (!$result) {
        http_response_code(500);
        die(json_encode(["error" => "Erro ao buscar usuários: " . $conn->error]));
    }

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);
    $conn->close();
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $cpf = $data["cpf"] ?? null;
    $nome = $data["nome"] ?? null;
    $data_nascimento = $data["data_nascimento"] ?? null;
    $sexo = $data["sexo"] ?? null;
    $nome_materno = $data["nome_materno"] ?? null;
    $email = $data["email"] ?? null;
    $telefone_celular = $data["telefone_celular"] ?? null;
    $cep = $data["cep"] ?? null;
    $rua = $data["rua"] ?? null;
    $numero_rua = $data["numero_rua"] ?? null;
    $complemento = $data["complemento"] ?? null;
    $bairro = $data["bairro"] ?? null;
    $estado = $data["estado"] ?? null;
    $cidade = $data["cidade"] ?? null;
    $login = $data["login"] ?? null;
    $senha = isset($data["senha"]) ? password_hash($data["senha"], PASSWORD_DEFAULT) : null;
    $admin = 0;

    if (!$cpf || !$nome || !$data_nascimento || !$sexo || !$nome_materno || !$email || !$telefone_celular || !$cep || !$rua || !$numero_rua || !$bairro || !$estado  || !$cidade || !$login || !$senha) {
        http_response_code(400);
        die(json_encode(["error" => "Todos os campos são obrigatórios no nosso teste aqui."]));
    }

    $sql = "INSERT INTO Usuario (cpf, nome, data_nascimento, sexo, nome_materno, email, telefone_celular, cep, rua, numero_rua, complemento, bairro, estado, cidade, login, senha, admin) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        http_response_code(500);
        die(json_encode(["error" => "Erro na preparação da querry: " . $conn->error]));
    }
    $stmt->bind_param("ssssssssssssssssi", $cpf, $nome, $data_nascimento, $sexo, $nome_materno, $email, $telefone_celular, $cep, $rua, $numero_rua, $complemento, $bairro, $estado, $cidade, $login, $senha, $admin);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["sucesso" => "Usuário cadastrado com sucesso!"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error no cadastro do Usuário. " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(["error" => "Método não suportado. Use GET ou POST."]);
$conn->close();
exit;
?>