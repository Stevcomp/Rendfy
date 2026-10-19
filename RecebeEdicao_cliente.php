<?php
// Inicia ou retoma a sessão ativa do usuário no servidor
session_start();

// Bloqueia o acesso se o usuário não estiver autenticado e redireciona para o login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: Index.php");
    exit;
}

// Garante que o script só processe requisições enviadas pelo método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: Perfil_cliente.php");
    exit;
}

// Importa as operações do banco de dados (que agora também trazem a conexão embutida)
require_once 'crud.php';

// Captura e forma o ID do usuário conectado com segurança
$usuario_id = intval($_SESSION['usuario_id']);
// == Seguro: prepared statements para buscar os dados atuais de perfil do usuário logado ==
$usuarioAtual = read($pdo, 'usuarios', "id = ?", [$usuario_id]);

// Força a desconexão se o registro do perfil do usuário atual não for localizado
if (!$usuarioAtual) {
    header("Location: Logout_cliente.php");
    exit;
}

// Captura e limpa os espaços em branco das entradas textuais do formulário
$nome = trim($_POST['nome'] ?? '');
$contato = trim($_POST['contato'] ?? '');
// Aplica uma expressão regular para filtrar e manter estritamente números no CPF digitado
$cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
// Captura as entradas mascaradas de texto voltadas para a alteração de senhas
$senha_atual = $_POST['senha_atual'] ?? '';
$nova_senha = $_POST['nova_senha'] ?? '';
$confirma = $_POST['confirma_senha'] ?? '';

// Bloco de controle para tratamento de exceções durante a atualização de dados
try {
    // Valida se o e-mail ou o CPF digitados já não pertencem a outra conta cadastrada
    if (!empty($contato) || !empty($cpf)) {
        $contatoVerif = !empty($contato) ? $contato : $usuarioAtual['contato'];
        $cpfVerif = !empty($cpf) ? $cpf : $usuarioAtual['cpf'];

        // == Seguro: verifica duplicidade no banco usando prepared statements ignorando o ID atual ==
        $duplicado = read(
            $pdo,
            'usuarios',
            "(cpf = ? OR contato = ?) AND id != ?",
            [$cpfVerif, $contatoVerif, $usuario_id]
        );

        // Bloqueia o processamento e retorna erro caso os dados já existam no banco
        if ($duplicado) {
            header("Location: Editar_cliente.php?erro=cpf_contato_existente");
            exit;
        }
    }

    // Inicializa o array responsável por mapear apenas os campos alterados
    $dadosAtualizados = [];
    if (!empty($nome))
        $dadosAtualizados['nome'] = $nome;
    if (!empty($contato))
        $dadosAtualizados['contato'] = $contato;
    if (!empty($cpf))
        $dadosAtualizados['cpf'] = $cpf;

    // Avalia as condições e processa a troca de senha caso as entradas tenham sido preenchidas
    if (!empty($nova_senha) || !empty($senha_atual)) {

        // Valida se a senha atual digitada corresponde ao hash cadastrado no banco de dados
        if (!password_verify($senha_atual, $usuarioAtual['senha'])) {
            header("Location: Editar_cliente.php?erro=senha_incorreta");
            exit;
        }

        // Verifica se a nova senha coincide perfeitamente com o campo de confirmação
        if ($nova_senha !== $confirma) {
            header("Location: Editar_cliente.php?erro=senhas_diferentes");
            exit;
        }

        // Aplica a criptografia segura e gera um novo hash para a nova senha do usuário
        $dadosAtualizados['senha'] = password_hash($nova_senha, PASSWORD_DEFAULT);
    }

    // Executa a query de atualização se houver alguma modificação detectada nos dados
    if (!empty($dadosAtualizados)) {
        // == Seguro: executa a modificação de dados filtrando estritamente pelo ID em prepared statement ==
        update($pdo, 'usuarios', $dadosAtualizados, "id = ?", [$usuario_id]);

        // Atualiza dinamicamente o nome armazenado na sessão caso o mesmo tenha sido editado
        if (isset($dadosAtualizados['nome'])) {
            $_SESSION['nome'] = $dadosAtualizados['nome'];
        }
    }

    // Redireciona o usuário para a tela de edição com a flag de sucesso ativada na URL
    header("Location: Editar_cliente.php?sucesso=1");
    exit;

    // Captura falhas de comunicação ou restrições geradas pelo banco de dados
} catch (PDOException $e) {
    header("Location: Editar_cliente.php?erro=banco");
    exit;
}