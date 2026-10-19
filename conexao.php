<?php
// Configurações do seu banco de dados (ajuste se os seus dados forem diferentes)
$host = 'localhost';
$port = 3306;
$dbname = 'rendify_calc_invest';
$usuario = 'root';
$senha = '';

try {
    // Cria a conexão utilizando a extensão PDO e salva na variável $pdo
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $senha);

    // Configura o PDO para disparar alertas caso ocorra algum erro de SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Caso a conexão falhe, exibe o erro e para a execução do script
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}
?>