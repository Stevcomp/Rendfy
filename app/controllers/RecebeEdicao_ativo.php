<?php
// Inicia ou retoma a sessão ativa do usuário no servidor
session_start();

// Bloqueia o acesso se o usuário não estiver autenticado e redireciona para o login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: ../../public/pages/Index.php");
    exit;
}

// Avalia se a requisição atual foi enviada utilizando o método seguro POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Importa as operações do banco de dados (que agora também trazem a conexão embutida)
    require_once __DIR__ . '/../models/crud.php';

    // Captura e forma o ID do usuário conectado com segurança
    $usuario_id = intval($_SESSION['usuario_id']);
    // Captura e força a conversão do ID do ativo que está sendo modificado
    $id = intval($_POST['id'] ?? 0);

    // Captura e limpa os espaços em branco das entradas textuais do formulário
    $nome = trim($_POST['nome'] ?? '');
    $categoria = $_POST['categoria'] ?? '';
    $tipo_fundo = trim($_POST['tipo_fundo'] ?? '');

    // Captura e converte as entradas numéricas para seus respectivos tipos de dados seguros
    $quantidade = intval($_POST['quantidade'] ?? 1);
    $vPago = floatval($_POST['vPago'] ?? 0);
    $vRecebido = floatval($_POST['vRecebido'] ?? 0);

    // Valida se os identificadores numéricos e as strings obrigatórias não estão vazios
    if ($id && !empty($nome) && !empty($categoria) && !empty($tipo_fundo)) {

        // Monta o mapa estruturado com os dados atualizados do papel financeiro
        $dadosAtualizados = [
            'nome' => $nome,
            'categoria' => $categoria,
            'tipo_fundo' => $tipo_fundo,
            'quantidade' => $quantidade,
            'vPago' => $vPago,
            'vRecebido' => $vRecebido,
        ];

        // == Seguro: Executa a atualização usando prepared statement validando o ID do ativo e o ID do dono ==
        update($pdo, 'ativos', $dadosAtualizados, "id = ? AND usuario_id = ?", [$id, $usuario_id]);
    }
}

// Redireciona o fluxo do navegador de volta para o painel principal de investimentos
header("Location: ../../public/pages/Dashboard.php");
exit;