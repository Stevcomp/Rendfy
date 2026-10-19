<?php// Inicia ou retoma a sessÃ£o ativa do usuÃ¡rio no servidor
session_start();

// Bloqueia o acesso se o usuÃ¡rio nÃ£o estiver autenticado e redireciona para o login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: Index.php");
    exit;
}

// Importa as operaÃ§Ãµes do banco de dados (que agora tambÃ©m trazem a conexÃ£o embutida)
require_once __DIR__ . '/../../app/models/crud.php';

// Captura e formata o ID do ativo enviado por parÃ¢metro e o ID do usuÃ¡rio conectado
$id = intval($_GET['id'] ?? 0);
$usuario_id = intval($_SESSION['usuario_id']);

// Valida se o ID do ativo Ã© vÃ¡lido, caso contrÃ¡rio retorna ao painel principal
if (!$id) {
    header("Location: Dashboard.php");
    exit;
}

// == Seguro: usa prepared statement para buscar se o ativo pertence ao usuÃ¡rio logado ==
$ativo = read($pdo, 'ativos', "id = ? AND usuario_id = ?", [$id, $usuario_id]);

// Redireciona o usuÃ¡rio ao painel se o ativo nÃ£o for encontrado ou nÃ£o pertencer a ele
if (!$ativo) {
    header("Location: Dashboard.php");
    exit;
}

// Lista estÃ¡tica contendo as segmentaÃ§Ãµes e classificaÃ§Ãµes vÃ¡lidas de fundos do sistema
$tipos_fundo = ['Papel', 'Tijolo', 'FOF', 'Agro', 'HÃ­brido', 'Desenvolvimento', 'AÃ§Ã£o', 'BDR', 'ETF', 'Outro'];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendify | Editar Ativo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="page-edit-asset">
    <header class="main-header">
        <div class="header-container">
            <div class="logo-area">
                <h1>Rendify ðŸ’¹</h1>
                <p>Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong></p>
            </div>
            <a href="../../app/controllers/Logout_cliente.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </header>

    <div class="editar-wrapper">
        <div class="editar-container">
            <h2><i class="fas fa-edit"></i> Editar Ativo</h2>
            <p class="subtitulo">Editando: <strong><?php echo strtoupper(htmlspecialchars($ativo['nome'])); ?></strong>
            </p>

            <form action="../../app/controllers/RecebeEdicao_ativo.php" method="POST" class="form-editar">
                <input type="hidden" name="id" value="<?php echo $ativo['id']; ?>">

                <div>
                    <label>Nome do Ativo</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($ativo['nome']); ?>" required>
                </div>

                <div>
                    <label>Categoria</label>
                    <select name="categoria" required>
                        <option value="fiis" <?php echo $ativo['categoria'] === 'fiis' ? 'selected' : ''; ?>>FIIs</option>
                        <option value="acao" <?php echo $ativo['categoria'] === 'acao' ? 'selected' : ''; ?>>AÃ§Ãµes
                        </option>
                    </select>
                </div>

                <div>
                    <label>Tipo do Fundo</label>
                    <select name="tipo_fundo" required>
                        <?php foreach ($tipos_fundo as $tipo): ?>
                            <option value="<?php echo $tipo; ?>" <?php echo ($ativo['tipo_fundo'] ?? '') === $tipo ? 'selected' : ''; ?>>
                                <?php echo $tipo; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label>Quantidade de Cotas</label>
                    <input type="number" name="quantidade" value="<?php echo $ativo['quantidade']; ?>" min="1" required>
                </div>

                <div>
                    <label>Valor Total Investido (R$)</label>
                    <input type="number" step="any" name="vPago" value="<?php echo $ativo['vPago']; ?>" required>
                </div>

                <div>
                    <label>R/C deste mÃªs (R$)</label>
                    <input type="number" step="any" name="vRecebido" value="<?php echo $ativo['vRecebido']; ?>"
                        required>
                </div>

                <div class="botoes-acao">
                    <button type="submit" class="btn-salvar"><i class="fas fa-save"></i> Salvar AlteraÃ§Ãµes</button>
                    <a href="Dashboard.php" class="btn-cancelar"><i class="fas fa-times"></i> Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
