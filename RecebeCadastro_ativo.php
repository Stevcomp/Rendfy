<?php
// Inicia ou retoma a sessão ativa do usuário no servidor
session_start();

// Bloqueia o acesso se o usuário não estiver autenticado e redireciona para o login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: Index.php");
    exit;
}

// Avalia se a requisição atual foi enviada utilizando o método seguro POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Importa as operações do banco de dados (que agora também trazem a conexão embutida)
    require_once 'crud.php';

    // Captura e forma o ID do usuário conectado com segurança
    $usuario_id = intval($_SESSION['usuario_id']);

    // Captura e limpa os espaços em branco das entradas textuais do formulário
    $nome = trim($_POST['nome'] ?? '');
    $categoria = $_POST['categoria'] ?? '';
    $tipo_fundo = trim($_POST['tipo_fundo'] ?? '');

    // Captura e converte as entradas numéricas para seus respectivos tipos de dados seguros
    $quantidade = intval($_POST['quantidade'] ?? 1);
    $vPago = floatval($_POST['vPago'] ?? 0);
    $vRecebido = floatval($_POST['vRecebido'] ?? 0);

    // Valida se todos os parâmetros obrigatórios foram devidamente preenchidos pelo usuário
    if (!empty($nome) && !empty($categoria) && !empty($tipo_fundo) && $usuario_id > 0) {

        // Monta o mapa de dados com as informações do novo ativo financeiro
        $novoAtivo = [
            'usuario_id' => $usuario_id,
            'nome' => $nome,
            'categoria' => $categoria,
            'tipo_fundo' => $tipo_fundo,
            'quantidade' => $quantidade,
            'vPago' => $vPago,
            'vRecebido' => $vRecebido,
        ];

        // Executa a instrução de inserção do registro do ativo na tabela correspondente
        create($pdo, 'ativos', $novoAtivo);
    }
}

// Redireciona o usuário de volta para o painel principal de investimentos
header("Location: Dashboard.php");
exit;