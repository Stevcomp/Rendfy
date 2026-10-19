<?php
// Inicia ou retoma a sessão ativa do usuário no servidor
session_start();

// Bloqueia o acesso se o usuário não estiver autenticado e redireciona para o login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: ../../public/pages/Index.php");
    exit;
}

// Importa as operações do banco de dados (que agora também trazem a conexão embutida)
require_once __DIR__ . '/../models/crud.php';

// Captura e força a conversão do ID do ativo recebido via parâmetro URL GET
$id = intval($_GET['id'] ?? 0);
// Captura e forma o ID do usuário conectado com segurança
$usuario_id = intval($_SESSION['usuario_id']);

// Valida se o identificador numérico do ativo foi enviado na requisição
if (!$id) {
    // Redireciona imediatamente para o painel principal caso o ID seja inválido
    header("Location: ../../public/pages/Dashboard.php");
    exit;
}

// == Seguro: Executa uma busca em prepared statement para validar se o ativo de fato pertence ao usuário conectado ==
$ativo = read($pdo, 'ativos', "id = ? AND usuario_id = ?", [$id, $usuario_id]);

// Bloqueia a operação caso o ativo não exista ou não pertença à sessão atual (proteção anti-IDOR)
if (!$ativo) {
    // Redireciona o fluxo de volta ao painel de investimentos de forma segura
    header("Location: ../../public/pages/Dashboard.php");
    exit;
}

// == Seguro: Executa a exclusão definitiva do ativo usando prepared statement vinculando o ID do registro e o ID do dono ==
delete($pdo, 'ativos', "id = ? AND usuario_id = ?", [$id, $usuario_id]);

// Redireciona o navegador de volta para o painel principal após a remoção bem-sucedida
header("Location: ../../public/pages/Dashboard.php");
exit;