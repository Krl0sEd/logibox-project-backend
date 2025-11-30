<?php
date_default_timezone_set('America/Sao_Paulo');

// ----- INÍCIO DO CÓDIGO DE DEBUG -----
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ----- FIM DO CÓDIGO DE DEBUG -----


// ----- INÍCIO DO CÓDIGO DE PERMISSÃO (CORS) -----
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit;
}
// ----- FIM DO CÓDIGO DE PERMISSÃO (CORS) -----

header("Content-Type: application/json; charset=UTF-8");

$host = "db";
$user = "CJJPW";
$password = "CJJPW";
$dbname = "Estoque";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Falha na conexão: " . $conn->connect_error]));
}

$data = json_decode(file_get_contents("php://input"), true);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id_produto = isset($_GET['id_produto']) ? $_GET['id_produto'] : null;
    $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;

    if ($id_produto) {
        $sql = "SELECT * FROM Estoque WHERE id_produto = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            http_response_code(500);
            die(json_encode(["error" => "Erro na preparação da query: " . $conn->error]));
        }

        $stmt->bind_param("i", $id_produto);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["error" => "Produto não encontrado."]);
        } else {
            $produto = $res->fetch_assoc();
            echo json_encode($produto);
        }

        $stmt->close();
        $conn->close();
        exit;
    }

    $sql = "SELECT * FROM Estoque";
    $result = $conn->query($sql);

    if (!$result) {
        http_response_code(500);
        die(json_encode(["error" => "Erro ao buscar produtos: " . $conn->error]));
    }

    $produtos = [];
    while ($row = $result->fetch_assoc()) {
        $produtos[] = $row;
    }

    echo json_encode($produtos);
    $conn->close();
    exit;
}

if ($method === 'POST') {
    $nome_produto = $data["nome_produto"] ?? null;
    $descricao = $data["descricao"] ?? null;
    $categoria = $data["categoria"] ?? null;
    $preco_unitario = $data["preco_unitario"] ?? null;
    $quantidade_estoque = $data["quantidade_estoque"] ?? null;
    $data_cadastro = $data["data_cadastro"] ?? null;
    $id_usuario = $data["id_usuario"] ?? null;

    if (!$nome_produto || !$descricao || !$categoria || !$preco_unitario || !$quantidade_estoque || !$data_cadastro || !$id_usuario) {
        http_response_code(400);
        die(json_encode(["error" => "Todos os campos são obrigatórios no nosso teste aqui."]));
    }

    $sql = "INSERT INTO Estoque (nome_produto, descricao, categoria, preco_unitario, quantidade_estoque, data_cadastro, id_usuario) VALUES (?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        http_response_code(500);
        die(json_encode(["error" => "Erro na preparação da query: " . $conn->error]));
    }

    $stmt->bind_param("ssssssi", $nome_produto, $descricao, $categoria, $preco_unitario, $quantidade_estoque, $data_cadastro, $id_usuario);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["sucesso" => "Produto cadastrado com sucesso!"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erro no cadastro do produto: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

if ($method === 'PUT') {
    $id_produto = $data["id_produto"] ?? null;
    $nome_produto = $data["nome_produto"] ?? null;
    $descricao = $data["descricao"] ?? null;
    $categoria = $data["categoria"] ?? null;
    $preco_unitario = $data["preco_unitario"] ?? null;
    $quantidade_estoque = $data["quantidade_estoque"] ?? null;

    if (!$id_produto || !$nome_produto || !$descricao || !$categoria || !$preco_unitario || !$quantidade_estoque) {
        http_response_code(400);
        die(json_encode(["error" => "Dados incompletos para atualização."]));
    }

    $sql = "UPDATE Estoque SET nome_produto=?, descricao=?, categoria=?, preco_unitario=?, quantidade_estoque=? WHERE id_produto=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        http_response_code(500);
        die(json_encode(["error" => "Erro na preparação do UPDATE: " . $conn->error]));
    }

    $stmt->bind_param("sssssi", $nome_produto, $descricao, $categoria, $preco_unitario, $quantidade_estoque, $id_produto);

    if ($stmt->execute()) {
        echo json_encode(["sucesso" => "Produto atualizado com sucesso!"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao atualizar: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

if ($method === 'DELETE') {
    // Vamos pegar o ID pela URL (ex: estoque.php?id_produto=10)
    $id_produto = $_GET['id_produto'] ?? null;

    if (!$id_produto) {
        http_response_code(400);
        die(json_encode(["error" => "ID do produto é obrigatório para exclusão."]));
    }

    $sql = "DELETE FROM Estoque WHERE id_produto = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        http_response_code(500);
        die(json_encode(["error" => "Erro na preparação do DELETE: " . $conn->error]));
    }

    $stmt->bind_param("i", $id_produto);

    if ($stmt->execute()) {
        echo json_encode(["sucesso" => "Produto excluído com sucesso!"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao excluir: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(["error" => "Método não suportado! Use GET ou POST."]);
$conn->close();
exit;
?>