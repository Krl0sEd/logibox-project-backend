<?php

// ----- DEBUG -----
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ----- CORS -----
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

header("Content-Type: application/json; charset=UTF-8");

// ----- CONEXÃO BD -----
$host = "db";
$user = "CJJPW";
$password = "CJJPW";
$dbname = "Estoque";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Falha na conexão: " . $conn->connect_error]);
    exit;
}

// ================================
// GET → Retornar quantidade de logs
// ================================
$sql = "SELECT COUNT(*) AS total FROM Log";
$res = $conn->query($sql);

if (!$res) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao buscar total de ações"]);
    exit;
}

$row = $res->fetch_assoc();

echo json_encode(["total_acoes" => $row["total"]]);

$conn->close();
exit;

?>