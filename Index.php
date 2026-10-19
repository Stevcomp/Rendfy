<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendify | Calculadora de Rendimento - Login</title>
    <link rel="stylesheet" href="style1.css">
    <link rel="icon" type="image/png" href="./Imagens/Icone.png">
</head>

<body>

    <?php
    // Mapeia e define flags booleanas seguras avaliando os parâmetros de feedback recebidos via URL
    $mostrar_erro = isset($_GET['erro']) && $_GET['erro'] == 1;
    $mostrar_sucesso = isset($_GET['sucesso']) && $_GET['sucesso'] == 1;
    $mostrar_conta_excluida = isset($_GET['conta_excluida']) && $_GET['conta_excluida'] == 1;
    ?>

    <?php if ($mostrar_erro): ?>
        <div class="error-banner">
            ⚠️ Informações incorretas! Por favor, verifique os dados de login, ou crie uma conta.
        </div>
    <?php endif; ?>

    <?php if ($mostrar_sucesso): ?>
        <script>
            // Dispara uma janela de aviso parabenizando o usuário pelo novo registro
            alert('Cadastro realizado com sucesso!');
            // Redireciona limpando os parâmetros da URL para evitar reenvios acidentais
            window.location.href = 'Index.php';
        </script>
    <?php endif; ?>

    <?php if ($mostrar_conta_excluida): ?>
        <script>
            // Exibe um aviso confirmando a remoção total dos registros do usuário
            alert('Sua conta foi excluída com sucesso. Até mais!');
            // Atualiza o estado da página limpando os gatilhos passados por GET
            window.location.href = 'Index.php';
        </script>
    <?php endif; ?>

    <?php
    // Inclui o arquivo contendo a estrutura de cabeçalho global e idêntica do sistema
    include 'Header.php'
        ?>

    <div class="container">

        <div class="box">
            <h1>Faça Seu Login Para Acessar Nosso Sistema</h1>

            <form method="POST" action="RecebeCadastro_cliente.php" autocomplete="off">
                <input type="text" placeholder="CPF (apenas números)" name="cpf" maxlength="11"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                <input type="password" placeholder="Senha" name="senha" required>
                <button type="submit">Entrar</button>
            </form>

            <div class="cadastro-link-area">
                Ainda não é cadastrado? <a href="CriarCadastro_cliente.php">Criar conta.</a>
            </div>

        </div>

    </div>

</body>

</html>