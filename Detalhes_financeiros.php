<?php
// Inicia ou retoma a sessão ativa do usuário no servidor
session_start();

// Bloqueia o acesso se o usuário não estiver autenticado e redireciona para o login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: Index.php");
    exit;
}

// Importa as operações do banco de dados
require_once __DIR__ . '/backend/database/crud.php';

// Captura e forma o ID do usuário conectado com segurança
$usuario_id = intval($_SESSION['usuario_id']);

// Busca os ativos ordenados por ID
$ativos = readAll($pdo, 'ativos', "usuario_id = ? ORDER BY id ASC", [$usuario_id]);

// Inicializa variáveis acumuladoras para o balanço financeiro geral
$totalInvestido = 0;
$totalRendimento = 0;

// Percorre a lista de ativos para calcular o montante investido total e o rendimento bruto do mês
foreach ($ativos as $ativo) {
    $totalInvestido += $ativo['vPago'];
    $totalRendimento += ($ativo['quantidade'] * $ativo['vRecebido']);
}

// Calcula a porcentagem geral de rentabilidade da carteira inteira
$rentabilidadeMensal = ($totalInvestido > 0)
    ? ($totalRendimento / $totalInvestido) * 100
    : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendify | Detalhes Financeiros</title>
    <link rel="stylesheet" href="frontend/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="page-style7">

    <header class="main-header">
        <div class="header-container">
            <div class="logo-area">
                <h1>Rendify 💹</h1>
                <h2>Detalhes Financeiros</h2>
            </div>
            <a href="Dashboard.php" class="btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
    </header>

    <main class="content-wrapper">

        <div class="card-rentabilidade">
            <p class="rentabilidade-label">Rentabilidade do Mês</p>
            <p class="rentabilidade-valor <?php echo $rentabilidadeMensal > 1 ? 'positivo' : 'negativo'; ?>">
                <?php echo number_format($rentabilidadeMensal, 2, ',', '.'); ?>%
            </p>
            <p class="rentabilidade-frase">
                <?php if (empty($ativos)): ?>
                    Nenhum ativo cadastrado ainda.
                <?php elseif ($rentabilidadeMensal >= 1): ?>
                    🎉 Este mês seus ativos renderam
                    <strong><?php echo number_format($rentabilidadeMensal, 2, ',', '.'); ?>%</strong>!!
                <?php else: ?>
                    ⚠️ Rendimento da carteira: <?php echo number_format($rentabilidadeMensal, 2, ',', '.'); ?>% este mês.
                <?php endif; ?>
            </p>
        </div>

        <div class="blocos-resumo">

            <div class="bloco-card">
                <div class="bloco-icone icone-azul">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="bloco-info">
                    <span class="bloco-label">Total Investido Acumulado</span>
                    <span class="bloco-valor">R$ <?php echo number_format($totalInvestido, 2, ',', '.'); ?></span>
                    <span class="bloco-sub">Soma de todos os valores pagos</span>
                </div>
            </div>

            <div class="bloco-card">
                <div class="bloco-icone icone-verde">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="bloco-info">
                    <span class="bloco-label">Rendimento Total do Mês</span>
                    <span class="bloco-valor valor-verde">R$
                        <?php echo number_format($totalRendimento, 2, ',', '.'); ?></span>
                    <span class="bloco-sub">Soma de (Qtd × R/C) de todos os ativos</span>
                </div>
            </div>

            <div class="bloco-card">
                <div class="bloco-icone icone-roxo">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="bloco-info">
                    <span class="bloco-label">Total de Ativos</span>
                    <span class="bloco-valor"><?php echo count($ativos); ?></span>
                    <span class="bloco-sub">Ativos cadastrados na carteira</span>
                </div>
            </div>

        </div>

        <?php if (!empty($ativos)): ?>
            <section class="tabela-container">
                <h3 class="tabela-titulo"><i class="fas fa-table"></i> Composição da Carteira</h3>
                <div class="tabela-wrapper">
                    <table class="tabela-ativos">
                        <thead>
                            <tr>
                                <th>Ativo</th>
                                <th>Categoria</th>
                                <th>Tipo</th>
                                <th>Cotas</th>
                                <th>Total Investido</th>
                                <th>Rend. do Mês</th>
                                <th>DY</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ativos as $ativo):
                                // USA O MESMO ARQUIVO DE CÁLCULO DO DASHBOARD
                                include 'DY_calculo.php';
                                $rendMes = $ativo['quantidade'] * $ativo['vRecebido'];
                                ?>
                                <tr>
                                    <td class="td-nome"><?php echo htmlspecialchars(strtoupper($ativo['nome'])); ?></td>
                                    <td><span
                                            class="badge-categoria"><?php echo strtoupper(htmlspecialchars($ativo['categoria'])); ?></span>
                                    </td>
                                    <td><span
                                            class="badge-tipo"><?php echo htmlspecialchars($ativo['tipo_fundo'] ?? '—'); ?></span>
                                    </td>
                                    <td><?php echo $ativo['quantidade']; ?></td>
                                    <td>R$ <?php echo number_format($totalAtivo ?? $ativo['vPago'], 2, ',', '.'); ?></td>
                                    <td class="valor-verde">R$ <?php echo number_format($rendMes, 2, ',', '.'); ?></td>
                                    <!-- APLICA O $rendimentoPct DO ARQUIVO DE CÁLCULO -->
                                    <td class="<?php echo $rendimentoPct <= 0 ? 'valor-vermelho' : 'valor-verde'; ?>">
                                        <?php echo number_format($rendimentoPct, 2, ',', '.'); ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="tr-total">
                                <td colspan="4">TOTAL</td>
                                <td>R$ <?php echo number_format($totalInvestido, 2, ',', '.'); ?></td>
                                <td class="valor-verde">R$ <?php echo number_format($totalRendimento, 2, ',', '.'); ?></td>
                                <td class="<?php echo $rentabilidadeMensal <= 0 ? 'valor-vermelho' : 'valor-verde'; ?>">
                                    <?php echo number_format($rentabilidadeMensal, 2, ',', '.'); ?>%
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>
        <?php endif; ?>

    </main>

</body>

</html>