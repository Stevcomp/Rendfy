<?php
// Inicia ou retoma a sessão ativa do usuário no servidor
session_start();

// Importa as operações do banco de dados (que agora também trazem a conexão embutida)
require_once __DIR__ . '/../models/crud.php';

// Avalia se a requisição atual foi enviada utilizando o método seguro POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Aplica uma expressão regular para filtrar e manter estritamente números no CPF digitado
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
    // Captura o texto puro da senha fornecida na tentativa de login
    $senha = $_POST['senha'] ?? '';

    // Valida se as credenciais obrigatórias de identificação não foram enviadas em branco
    if (empty($cpf) || empty($senha)) {
        // Redireciona o fluxo para a página inicial com o parâmetro indicativo de falha
        header("Location: ../../public/pages/Index.php?erro=1");
        exit;
    }

    // == Seguro: usa prepared statement via parâmetro para buscar o usuário pelo CPF unificado ==
    $usuario = read($pdo, 'usuarios', "cpf = ?", [$cpf]);

    // Compara o registro localizado e valida o hash da senha usando criptografia nativa
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Define as chaves globais de sessão autenticando e guardando a identidade do cliente
        $_SESSION['logado'] = true;
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];

        // Redireciona o usuário validado diretamente para o painel de monitoramento de ativos
        header("Location: ../../public/pages/Dashboard.php");
        exit;
    }

    // Redireciona para o login sinalizando erro caso a senha ou o CPF estejam incorretos
    header("Location: ../../public/pages/Index.php?erro=1");
    exit;

    // Bloco de escape caso o script receba uma requisição direta fora do método esperado
} else {
    // Redireciona o navegador de volta à tela inicial de login por segurança
    header("Location: ../../public/pages/Index.php");
    exit;
}