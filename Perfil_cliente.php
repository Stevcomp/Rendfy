<?php
// Inicia ou retoma a sessão ativa do usuário no servidor
session_start();

// Bloqueia o acesso se o usuário não estiver autenticado e redireciona para o login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: Index.php");
    exit;
}

// Importa as operações do banco de dados (que agora também trazem a conexão embutida)
require_once 'crud.php';

// Captura e forma o ID do usuário conectado com segurança
$usuario_id = intval($_SESSION['usuario_id']);

// == Seguro: prepared statements substituindo concatenação direta na query ==
$dadosPerfil = read($pdo, 'usuarios', "id = ?", [$usuario_id]);

// Força a desconexão se o registro do perfil do usuário atual não for localizado
if (!$dadosPerfil) {
    header("Location: Logout_cliente.php");
    exit;
}

// Define uma máscara estática padrão para ocultar a exibição da senha real na interface
$senhaMascarada = '••••••••';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendify | Meu Perfil</title>
    <link rel="stylesheet" href="style4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

    <header class="main-header">
        <div class="header-container">
            <div class="logo-area">
                <h1>Rendify 💹</h1>
                <h2>Meu Perfil</h2>
            </div>
            <a href="Dashboard.php" class="btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
    </header>

    <main class="content-wrapper">
        <section class="perfil-container">

            <div class="perfil-header">
                <div class="perfil-avatar">
                    <?php
                    // Avalia se o registro possui um nome de arquivo válido e se o arquivo físico existe no diretório
                    if (!empty($dadosPerfil['foto_perfil']) && file_exists('uploads/' . $dadosPerfil['foto_perfil'])) {
                        $fotoExibicao = 'uploads/' . $dadosPerfil['foto_perfil'];
                        echo "<img src='{$fotoExibicao}' alt='Foto de Perfil'>";
                    } else {
                        echo "<i class='fas fa-user'></i>";
                    }
                    ?>
                </div>

                <form action="RecebeFoto_cliente.php" method="POST" enctype="multipart/form-data" id="form-foto">
                    <label for="input-foto" class="btn-upload-foto">
                        <i class="fas fa-camera"></i> Alterar Foto
                    </label>
                    <input type="file" name="foto" id="input-foto" accept="image/*"
                        onchange="document.getElementById('form-foto').submit();">
                </form>

                <h2><?php echo htmlspecialchars($dadosPerfil['nome']); ?></h2>
                <p>Membro do Rendify</p>
            </div>

            <div class="perfil-info-grupo">
                <span class="perfil-label">Nome Completo</span>
                <div class="perfil-valor"><?php echo htmlspecialchars($dadosPerfil['nome']); ?></div>
            </div>

            <div class="perfil-info-grupo">
                <span class="perfil-label">E-mail de Contato</span>
                <div class="perfil-valor"><?php echo htmlspecialchars($dadosPerfil['contato']); ?></div>
            </div>

            <div class="perfil-info-grupo">
                <span class="perfil-label">CPF</span>
                <div class="perfil-valor">
                    <?php
                    // Sanitiza e formata os 11 dígitos sequenciais do CPF adicionando pontos e traço
                    $cpfLimpo = htmlspecialchars($dadosPerfil['cpf']);
                    echo substr($cpfLimpo, 0, 3) . '.' . substr($cpfLimpo, 3, 3) . '.' . substr($cpfLimpo, 6, 3) . '-' . substr($cpfLimpo, 9, 2);
                    ?>
                </div>
            </div>

            <div class="perfil-info-grupo">
                <span class="perfil-label">Senha</span>
                <div class="perfil-valor perfil-senha"><?php echo $senhaMascarada; ?></div>
            </div>

            <div class="perfil-info-grupo">
                <span class="perfil-label">Conta Criada Em</span>
                <div class="perfil-valor">
                    <?php echo date('d/m/Y \à\s H:i', strtotime($dadosPerfil['criado_em'])); ?>
                </div>
            </div>

            <a href="Editar_cliente.php" class="btn-alterar-dados">
                <i class="fas fa-user-edit"></i> Alterar Dados
            </a>

            <a href="Dashboard.php" class="btn-voltar-painel">
                <i class="fas fa-chart-line"></i> Ir para Meus Investimentos
            </a>

            <a href="Logout_cliente.php" class="btn-sair">
                <i class="fas fa-sign-out-alt"></i> Sair da Conta
            </a>

            <a href="#" class="btn-excluir-conta"
                onclick="if(confirm('Tem certeza que deseja excluir sua conta? Esta ação é irreversível e todos os seus dados serão apagados.')) window.location.href='Excluir_cliente.php';">
                <i class="fas fa-trash-alt"></i> Excluir Minha Conta Permanentemente
            </a>

        </section>
    </main>

</body>

</html>