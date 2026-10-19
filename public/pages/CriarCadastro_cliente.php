<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendify | Calculadora de Rendimento - Criar Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="page-register">

    <?php include __DIR__ . '/../partials/Header.php' ?>
    <!-- Inclui o cabeçalho reutilizável do sistema -->

    <?php
    // Verifica erro de cadastro duplicado via parâmetro da URL
    if (isset($_GET['erro']) && $_GET['erro'] === 'cpf_contato_existente') {
        echo "
        <script>
            // Exibe alerta e redireciona para a tela inicial
            alert('Esta conta já está cadastrada');
            window.location.href = 'Index.php';
        </script>
        ";
    }
    ?>

    <div class="container">

        <div class="box">
            <h1>Crie sua Conta Para Acessar Nosso Sistema</h1>

            <form method="POST" action="../../app/controllers/RecebeNovoCadastro_cliente.php" autocomplete="off">
                <!-- Envia os dados do cadastro para o backend -->

                <input type="text" placeholder="Nome Completo" name="nome_cliente" required>
                <input type="text" placeholder="E-mail" name="contato" required>

                <!-- Garante que o CPF aceite apenas números -->
                <input type="text" placeholder="CPF (apenas números)" name="cpf" maxlength="11"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>

                <input type="password" placeholder="Senha" name="senha" required>

                <button type="submit">Criar Conta</button>
            </form>

        </div>

    </div>

</body>

</html>