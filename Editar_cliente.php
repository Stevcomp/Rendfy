<?php
// Inicia ou retoma a sessão ativa do usuário no servidor
session_start();

// Bloqueia o acesso se o usuário não estiver autenticado e redireciona para o login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: Index.php");
    exit;
}

// Importa as operações do banco de dados (que agora também trazem a conexão embutida)
require_once __DIR__ . '/backend/database/crud.php';

// Captura e forma o ID do usuário conectado com segurança
$usuario_id = intval($_SESSION['usuario_id']);
// == Seguro: prepared statements para buscar os dados de perfil do usuário logado ==
$dadosPerfil = read($pdo, 'usuarios', "id = ?", [$usuario_id]);

// Força a desconexão se o registro do perfil do usuário atual não for localizado
if (!$dadosPerfil) {
    header("Location: backend/Logout_cliente.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendify | Alterar Dados</title>
    <link rel="stylesheet" href="frontend/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="page-style5">

    <header class="main-header">
        <div class="header-container">
            <div class="logo-area">
                <h1>Rendify 💹</h1>
                <h2>Alterar Dados</h2>
            </div>
            <a href="Perfil_cliente.php" class="btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar ao Perfil
            </a>
        </div>
    </header>

    <main class="content-wrapper">
        <section class="alterar-container">

            <div class="alterar-header">
                <div class="alterar-avatar">
                    <i class="fas fa-user-edit"></i>
                </div>
                <h2 class="alterar-titulo">Editar Informações</h2>
                <p class="alterar-subtitulo">Deixe em branco os campos que não deseja alterar</p>
            </div>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alerta alerta-erro">
                    <?php if ($_GET['erro'] === 'cpf_contato_existente'): ?>
                        <i class="fas fa-exclamation-circle"></i> Este e-mail ou CPF já está em uso por outro usuário.
                    <?php elseif ($_GET['erro'] === 'senha_incorreta'): ?>
                        <i class="fas fa-exclamation-circle"></i> A senha atual informada está incorreta.
                    <?php elseif ($_GET['erro'] === 'senhas_diferentes'): ?>
                        <i class="fas fa-exclamation-circle"></i> A nova senha e a confirmação não coincidem.
                    <?php elseif ($_GET['erro'] === 'senha_curta'): ?>
                        <i class="fas fa-exclamation-circle"></i> A nova senha deve ter no mínimo 8 caracteres.
                    <?php else: ?>
                        <i class="fas fa-exclamation-circle"></i> Ocorreu um erro. Tente novamente.
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alerta alerta-sucesso">
                    <i class="fas fa-check-circle"></i> Dados updated com sucesso!
                </div>
            <?php endif; ?>

            <form action="backend/RecebeEdicao_cliente.php" method="POST" class="form-alterar">

                <div class="form-grupo">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($dadosPerfil['nome']); ?>">
                </div>

                <div class="form-grupo">
                    <label>E-mail de Contato</label>
                    <input type="text" name="contato" value="<?php echo htmlspecialchars($dadosPerfil['contato']); ?>">
                </div>

                <div class="form-grupo">
                    <label>CPF <span class="campo-obs">(apenas números)</span></label>
                    <input type="text" name="cpf" maxlength="11"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                        value="<?php echo htmlspecialchars($dadosPerfil['cpf']); ?>">
                </div>

                <div class="separador">
                    <span>Alterar Senha</span>
                </div>

                <div class="form-grupo">
                    <label>Senha Atual <span class="campo-obs">(obrigatório para trocar a senha)</span></label>
                    <div class="input-senha-wrapper">
                        <input type="password" name="senha_atual" id="senha_atual" placeholder="Digite sua senha atual">
                        <button type="button" class="btn-olho" onclick="toggleSenha('senha_atual', 'icone_atual')">
                            <i id="icone_atual" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-grupo">
                    <label>Nova Senha <span class="campo-obs">(mínimo 8 caracteres)</span></label>
                    <div class="input-senha-wrapper">
                        <input type="password" name="nova_senha" id="nova_senha" placeholder="Digite a nova senha">
                        <button type="button" class="btn-olho" onclick="toggleSenha('nova_senha', 'icone_nova')">
                            <i id="icone_nova" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-grupo">
                    <label>Confirmar Nova Senha</label>
                    <div class="input-senha-wrapper">
                        <input type="password" name="confirma_senha" id="confirma_senha"
                            placeholder="Repita a nova senha">
                        <button type="button" class="btn-olho"
                            onclick="toggleSenha('confirma_senha', 'icone_confirma')">
                            <i id="icone_confirma" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="botoes-acao">
                    <button type="submit" class="btn-salvar">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="Perfil_cliente.php" class="btn-cancelar">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>

            </form>
        </section>
    </main>

    <script>
        // Função responsável por alternar a visibilidade do texto entre oculto e visível nos inputs
        function toggleSenha(inputId, iconeId) {
            const input = document.getElementById(inputId);
            const icone = document.getElementById(iconeId);
            if (input.type === 'password') {
                input.type = 'text';
                icone.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icone.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

</body>

</html>