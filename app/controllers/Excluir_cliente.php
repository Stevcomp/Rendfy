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

// Captura e forma o ID do usuário conectado com segurança
$usuario_id = intval($_SESSION['usuario_id']);

// Bloco de controle para tratamento de exceções durante a remoção de dados
try {
    // Remove explicitamente os ativos vinculados à conta para evitar registros órfãos
    delete($pdo, 'ativos', "usuario_id = ?", [$usuario_id]);
    // Executa a exclusão definitiva do perfil do usuário na tabela principal
    delete($pdo, 'usuarios', "id = ?", [$usuario_id]);

    // Limpa todas as variáveis salvas e encerra a sessão atual do sistema
    session_destroy();

    // Redireciona o usuário para a página inicial com um gatilho de sucesso na URL
    header("Location: ../../public/pages/Index.php?conta_excluida=1");
    exit;

    // Captura falhas de comunicação ou restrições geradas pelo banco de dados
} catch (PDOException $e) {
    // Redireciona de volta ao perfil exibindo uma sinalização de falha no banco
    header("Location: ../../public/pages/Perfil_cliente.php?erro=banco");
    exit;
}