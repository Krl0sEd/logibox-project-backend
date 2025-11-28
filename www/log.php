<?php
date_default_timezone_set('America/Sao_Paulo');

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$host = "db";
$user = "CJJPW";
$pass = "CJJPW";
$dbname = "Estoque";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Erro na conexão"]));
}

$sql = "
    SELECT
        l.data_hora_login AS data,
        u.nome AS usuario,
        'Login' AS acao
    FROM Log l
    INNER JOIN Usuario u ON u.id_usuario = l.id_usuario
    ORDER BY l.data_hora_login DESC
";

$result = $conn->query($sql);

$logs = [];

while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

echo json_encode($logs, JSON_UNESCAPED_UNICODE);
$conn->close();
?>