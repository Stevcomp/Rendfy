<?php

// ── Valor total investido naquele ativo ──
$totalAtivo = $ativo['vPago'];

// ── Rendimento total do mês = rendimento por cota × número de cotas ──
$rendimentoTotalMensal = $ativo['quantidade'] * $ativo['vRecebido'];

// ── DY mensal (%): quanto o ativo rendeu em relação ao capital investido
// Protegido contra divisão por zero com operador ternário ──
$rendimentoPct = ($ativo['vPago'] > 0) ? ($rendimentoTotalMensal / $ativo['vPago']) * 100 : 0;

?>