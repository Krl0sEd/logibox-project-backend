<?php
date_default_timezone_set('America/Sao_Paulo');

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

// Conexão
$host = "db";
$user = "CJJPW";
$pass = "CJJPW";
$dbname = "Estoque";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Erro na conexão com o banco"]));
}

/* ============================================================
   1) TOTAL DE PRODUTOS POR CATEGORIA
============================================================ */

$sqlCategorias = "
    SELECT categoria,
           COUNT(id_produto) AS quantidade
    FROM Estoque
    GROUP BY categoria
";

$res1 = $conn->query($sqlCategorias);

$categorias = [];
while ($row = $res1->fetch_assoc()) {
    $categorias[] = $row;
}

/* ============================================================
   2) TOP 5 ITENS COM MAIOR VALOR TOTAL EM ESTOQUE
============================================================ */

$sqlTopValor = "
    SELECT nome_produto AS nome,
           (preco_unitario * quantidade_estoque) AS total
    FROM Estoque
    ORDER BY total DESC
    LIMIT 5
";

$res2 = $conn->query($sqlTopValor);

$top_valor = [];
while ($row = $res2->fetch_assoc()) {
    $top_valor[] = $row;
}

/* ============================================================
   JSON FINAL
============================================================ */

echo json_encode([
    "categorias" => $categorias,
    "top_valor" => $top_valor
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>