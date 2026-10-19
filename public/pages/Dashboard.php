<?php// ── Inicia a sessão para acessar variáveis de sessão do usuário ──
session_start();

// ── Controle de acesso: redireciona para login se o usuário não estiver autenticado ──
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: Index.php");
    exit;
}

// ── Define o fuso horário e calcula a saudação com base na hora atual ──
date_default_timezone_set('America/Sao_Paulo');
$horaAtual = intval(date('H'));

if ($horaAtual >= 5 && $horaAtual < 12) {
    $saudacao = "Bom dia";
} elseif ($horaAtual >= 12 && $horaAtual < 18) {
    $saudacao = "Boa tarde";
} else {
    $saudacao = "Boa noite";
}

// ── Carrega o arquivo de funções CRUD (readAll, create, update, delete) ──
require_once __DIR__ . '/../../app/models/crud.php';

// ── Recupera o ID do usuário logado e o filtro de categoria da URL ──
$usuario_id = intval($_SESSION['usuario_id']);
$filtro = $_GET['filtro'] ?? 'todos'; // Padrão: exibe todos os ativos

// ── Busca os ativos do usuário conforme o filtro selecionado (FIIs, Ações ou Todos) ──
// Usa prepared statements para evitar SQL Injection
if ($filtro === 'fiis') {
    $estoque = readAll(
        $pdo,
        'ativos',
        "usuario_id = ? AND categoria = ? ORDER BY id DESC",
        [$usuario_id, 'fiis']
    );
} elseif ($filtro === 'acao') {
    $estoque = readAll(
        $pdo,
        'ativos',
        "usuario_id = ? AND categoria = ? ORDER BY id DESC",
        [$usuario_id, 'acao']
    );
} else {
    $estoque = readAll(
        $pdo,
        'ativos',
        "usuario_id = ? ORDER BY id DESC",
        [$usuario_id]
    );
}


// ── Otimização: evita query duplicada.
// Se o filtro for "todos", reutiliza $estoque. Caso contrário, busca todos os ativos
// separadamente para calcular os totais gerais do rodapé (independente do filtro ativo) ──
$todosAtivos = ($filtro === 'todos')
    ? $estoque
    : readAll($pdo, 'ativos', "usuario_id = ? ORDER BY id DESC", [$usuario_id]);

// ── Calcula o total geral investido e conta ativos com rendimento abaixo de 1% (inadequados)
// ⚠️ ATENÇÃO: aqui o cálculo NÃO multiplica pela quantidade, diferente dos cards.
// Correção recomendada: usar ($item['quantidade'] * $item['vRecebido']) no numerador ──
$totalGeralInvestido = 0;
$ativosInadequados = 0;

foreach ($todosAtivos as $item) {
    $totalGeralInvestido += $item['vPago'];
    
    // Multiplica o valor por cota pela quantidade para ter o rendimento TOTAL do ativo
    $rendimentoTotalAtivo = $item['quantidade'] * $item['vRecebido'];
    
    // Calcula a porcentagem real sobre o valor investido
    $rendimentoPorcentagem = ($item['vPago'] > 0) ? ($rendimentoTotalAtivo / $item['vPago']) * 100 : 0;
    
    if ($rendimentoPorcentagem < 1) {
        $ativosInadequados++;
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendify | Calculadora de Rendimento - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="page-dashboard">

    <header class="main-header">
        <div class="header-container">
            <div class="logo-area">
                <h1>Rendify 💹</h1>
                <h2><?php echo $saudacao . ", " . htmlspecialchars($_SESSION['nome']); ?>! 👋</h2>
            </div>
            <a href="Perfil_cliente.php" class="btn-perfil">
                <i class="fas fa-user-circle"></i> Meu Perfil
            </a>
        </div>
    </header>

    <main class="content-wrapper">
        <section class="cadastro-container">
            <h2><i class="fas fa-plus-circle"></i> Cadastrar Novo Ativo</h2>
            <form action="../../app/controllers/RecebeCadastro_ativo.php" method="POST" class="form-cadastro">
                <div class="input-grupo">
                    <input type="text" name="nome" placeholder="Nome do ativo" required>
                </div>
                <div class="input-grupo">
                    <select name="categoria" required>
                        <option value="" disabled selected>Categoria</option>
                        <option value="fiis">FIIs</option>
                        <option value="acao">Ações</option>
                    </select>
                </div>
                <div class="input-grupo">
                    <select name="tipo_fundo" required>
                        <option value="" disabled selected>Tipo do Fundo</option>
                        <option value="Papel">Papel</option>
                        <option value="Tijolo">Tijolo</option>
                        <option value="FOF">FOF</option>
                        <option value="Agro">Agro</option>
                        <option value="Híbrido">Híbrido</option>
                        <option value="Desenvolvimento">Desenvolvimento</option>
                        <option value="Ação">Ação</option>
                        <option value="BDR">BDR</option>
                        <option value="ETF">ETF</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>
                <div class="input-grupo">
                    <input type="number" name="quantidade" placeholder="Quantidade" min="1" required>
                </div>
                <div class="input-grupo">
                    <input type="number" step="any" name="vPago" placeholder="V/T Investido" required>
                </div>
                <div class="input-grupo">
                    <input type="number" step="any" name="vRecebido" placeholder="R/C Deste mês" required>
                </div>
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Salvar</button>
            </form>
        </section>

        <div class="nav-filtros-abaixo">
            <a href="Dashboard.php?filtro=todos"
                class="btn-filtro <?php echo $filtro === 'todos' ? 'ativo' : ''; ?>">Todos</a>
            <a href="Dashboard.php?filtro=fiis"
                class="btn-filtro <?php echo $filtro === 'fiis' ? 'ativo' : ''; ?>">FIIs</a>
            <a href="Dashboard.php?filtro=acao"
                class="btn-filtro <?php echo $filtro === 'acao' ? 'ativo' : ''; ?>">Ações</a>
        </div>

        <section class="inventario-container">
            <?php if (!empty($estoque)): ?>

                <?php foreach ($estoque as $ativo):
                    // ── Sistema que calcula o DY para facilitar o mesmo calculo em outras páginas ──
                    include __DIR__ . '/../../app/views/DY_calculo.php';
                    ?>

                    <div class="ativo-linha-container">

                        <div class="box-esquerdo-info">
                            <div class="card-acoes">
                                <a href="Editar_ativo.php?id=<?php echo $ativo['id']; ?>" class="btn-acao-edicao btn-editar"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="../../app/controllers/RecebeExclusao_ativo.php?id=<?php echo $ativo['id']; ?>"
                                    class="btn-acao-edicao btn-excluir" title="Excluir">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>

                            <div class="produto-info-area">
                                <span
                                    class="produto-categoria"><?php echo strtoupper(htmlspecialchars($ativo['categoria'])); ?></span>
                                <h4 class="produto-nome"><?php echo htmlspecialchars($ativo['nome']); ?></h4>

                                <div class="produto-status">
                                    <?php if ($rendimentoPct < 1): ?>
                                        <span class="status-badge badge-critico">Inadequado</span>
                                    <?php else: ?>
                                        <span class="status-badge badge-adequado">Adequado</span>
                                    <?php endif; ?>
                                </div>

                                <div class="produto-dados">
                                    <div class="dado-item">
                                        <span
                                            class="valor-destaque"><?php echo $ativo['quantidade'] . ' ' . ($ativo['quantidade'] == 1 ? 'Cota' : 'Cotas'); ?></span>
                                    </div>
                                    <div class="dado-item">
                                        <span class="dado-tipo-fundo">
                                            <?php echo htmlspecialchars($ativo['tipo_fundo'] ?? '—'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box-direito-rendimentos">
                            <div class="rendimento-dados-grid">
                                <div class="rendimento-item-box">
                                    <span class="rendimento-label">Total Investido</span>
                                    <span class="valor-destaque valor-branco">R$
                                        <?php echo number_format($totalAtivo, 2, ',', '.'); ?></span>
                                </div>

                                <div class="rendimento-item-box">
                                    <span class="rendimento-label">Rendimento Recebido</span>
                                    <span class="valor-destaque valor-verde">R$
                                        <?php echo number_format($ativo['quantidade'] * $ativo['vRecebido'], 2, ',', '.'); ?></span>
                                    <small class="valor-sub">R$
                                        <?php echo number_format($ativo['vRecebido'], 2, ',', '.'); ?>/cota</small>
                                </div>

                                <div class="rendimento-item-box">
                                    <span class="rendimento-label">Dividend Yield</span>
                                    <span
                                        class="valor-destaque <?php echo ($rendimentoPct < 1) ? 'valor-vermelho' : 'valor-verde'; ?>">
                                        <?php echo number_format($rendimentoPct, 2, ',', '.') . '%'; ?>
                                    </span>
                                    <small class="valor-sub">Rendimento Mensal</small>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="sem-ativos">Nenhum investimento cadastrado.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="rodape-status">
        <div class="status-container">
            <span>
                <?php if (empty($estoque)): ?>
                    <i class="fas fa-info-circle icone-neutro"></i>
                    <span class="alerta-neutro">Nenhum ativo cadastrado ainda.</span>
                <?php elseif ($ativosInadequados == 0): ?>
                    <i class="fas fa-check-circle icone-verde"></i>
                    <span class="alerta-verde">Todos os ativos com rendimento adequado!</span>
                <?php else: ?>
                    <i class="fas fa-exclamation-triangle icone-vermelho"></i>
                    <span class="alerta-vermelho">Alerta: <?php echo $ativosInadequados; ?> ativo(s) com rendimento abaixo
                        de 1%.</span>
                <?php endif; ?>
            </span>
            <div class="status-financeiro">
                <span class="valor-total-label">Total Investido Acumulado</span>
                <span class="valor-total-destaque">R$
                    <?php echo number_format($totalGeralInvestido, 2, ',', '.'); ?></span>
                <a href="Detalhes_financeiros.php" class="btn-ver-detalhes">
                    <i class="fas fa-chart-bar"></i> Ver detalhes
                </a>
            </div>
        </div>
    </footer>

</body>

</html>