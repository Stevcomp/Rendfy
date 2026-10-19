<?php
// Inicia ou retoma a sessão ativa do usuário no servidor
session_start();

// Importa as operações do banco de dados (que agora também trazem a conexão embutida)
require_once 'crud.php';

// Garante que o script só processe requisições enviadas pelo método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Captura e limpa os espaços em branco das entradas textuais do formulário
    $nome = trim($_POST['nome_cliente'] ?? '');
    $contato = trim($_POST['contato'] ?? '');
    // Aplica uma expressão regular para filtrar e manter estritamente números no CPF digitado
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
    // Captura o texto puro da senha fornecida para a criação da conta
    $senha = $_POST['senha'] ?? '';

    // Valida se algum dos parâmetros obrigatórios de cadastro foi enviado em branco
    if (empty($nome) || empty($contato) || empty($cpf) || empty($senha)) {
        header("Location: CriarCadastro_cliente.php?erro=campos_vazios");
        exit;
    }

    // Bloco de controle para tratamento de exceções durante a inserção do novo registro
    try {
        // == Seguro: usa prepared statement via parâmetro para verificar se o CPF ou E-mail já existem ==
        $usuarioExiste = read($pdo, 'usuarios', "cpf = ? OR contato = ?", [$cpf, $contato]);

        // Bloqueia o cadastro e retorna erro caso os dados já constem no banco de dados
        if ($usuarioExiste) {
            header("Location: CriarCadastro_cliente.php?erro=cpf_contato_existente");
            exit;
        }

        // Aplica a criptografia segura gerando o hash hash da senha informada
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Monta o mapa estruturado com os dados do novo usuário para inserção
        $dadosUsuario = [
            'nome' => $nome,
            'contato' => $contato,
            'cpf' => $cpf,
            'senha' => $senhaHash,
        ];

        // Executa a instrução de criação do registro do cliente na tabela de usuários
        create($pdo, 'usuarios', $dadosUsuario);

        // Redireciona para a tela inicial disparando o gatilho de sucesso na URL
        header("Location: Index.php?sucesso=1");
        exit;

        // Captura falhas de comunicação ou restrições geradas pelo banco de dados
    } catch (PDOException $e) {
        header("Location: CriarCadastro_cliente.php?erro=banco");
        exit;
    }

    // Bloco de escape caso o script receba uma requisição direta fora do método esperado
} else {
    header("Location: CriarCadastro_cliente.php");
    exit;
}