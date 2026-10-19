<?php
// Garante que a conexão com o banco seja importada junto com o CRUD
require_once 'conexao.php';

// Funções CRUD genéricas reutilizáveis para qualquer tabela

function create($pdo, $table, array $data)
{
    // Monta e executa um INSERT dinâmico com base nos dados recebidos
    $columns      = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    $sql          = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt         = $pdo->prepare($sql);
    $stmt->execute(array_values($data));
    return $pdo->lastInsertId();
}

function readAll($pdo, $table, $where = null, $params = [])
{
    // Busca múltiplos registros com condição opcional
    $sql = "SELECT * FROM $table";
    if ($where) $sql .= " WHERE $where";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function read($pdo, $table, $where = null, $params = [])
{
    // Busca um único registro
    $sql = "SELECT * FROM $table";
    if ($where) $sql .= " WHERE $where";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function update($pdo, $table, array $data, $where, $params = [])
{
    // Atualiza campos dinamicamente com base nos dados informados
    $set = [];
    foreach ($data as $column => $value) {
        $set[] = "$column = ?";
    }
    $set  = implode(', ', $set);
    $sql  = "UPDATE $table SET $set WHERE $where";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge(array_values($data), $params));
    return $stmt->rowCount();
}

function delete($pdo, $table, $where, $params = [])
{
    // Remove registros com base em uma condição
    $sql  = "DELETE FROM $table WHERE $where";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}