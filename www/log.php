<?php
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

$id_usuario = $data["id_usuario"] ?? null;
$tipo_2fa = $data["tipo_2fa"] ?? null;
$data_hora_login = date("Y-m-d H:i:s");

if (!$id_usuario || !$tipo_2fa) {
    die(json_encode(["error" => "Campos obrigatórios: id_usuario e tipo_2fa."]));
}

$sql = "INSERT INTO Log (id_usuario, data_hora_login, tipo_2fa) VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["error" => "Erro na preparação da query: " . $conn->error]));
}

$stmt->bind_param("iss", $id_usuario, $data_hora_login, $tipo_2fa);

if ($stmt->execute()) {
    echo json_encode(["sucesso" => "Usuário logado com sucesso!"]);
}

 else {
    echo json_encode(["error" => "Erro ao logar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>