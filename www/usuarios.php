<?php
date_default_timezone_set('America/Sao_Paulo');

// Configurações iniciais e CORS
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Conexão
$host = "db";
$user = "CJJPW";
$password = "CJJPW";
$dbname = "Estoque";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["erro" => "Falha na conexão: " . $conn->connect_error]));
}

$method = $_SERVER['REQUEST_METHOD'];

// --- GET: Buscar usuários ---
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        // Busca um usuário específico pelo id_usuario
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM Usuario WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        echo json_encode($result->fetch_assoc());
    } else {
        // Lista todos (selecionando apenas colunas essenciais para a tabela)
        $sql = "SELECT id_usuario, nome, admin, cpf, sexo FROM Usuario";
        $result = $conn->query($sql);

        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        echo json_encode($usuarios);
    }
}

// --- DELETE: Deletar usuário ---
elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;

    if ($id) {
        $stmt = $conn->prepare("DELETE FROM Usuario WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["sucesso" => true]);
        } else {
            http_response_code(500);
            echo json_encode(["erro" => "Erro ao deletar: " . $stmt->error]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["erro" => "ID não fornecido."]);
    }
}

$conn->close();
?>