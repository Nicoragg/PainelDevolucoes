<?php
require_once __DIR__ . "/../models/Devolucao.php";

// Captura filtros
$filtros = [
    'fornecedor' => $_GET['fornecedor'] ?? '',
    'loja'       => $_GET['loja'] ?? '',
    'motivo'     => $_GET['motivo'] ?? '',
    'data_inicio'=> $_GET['data_inicio'] ?? '',
    'data_fim'   => $_GET['data_fim'] ?? ''
];

// Busca todos os registros filtrados
$devolucoes = Devolucao::allFiltered($filtros);

$resumo         = Devolucao::getResumo($filtros);
$resumoPendentes= Devolucao::getPendentes($filtros);
$dadosStatus    = Devolucao::getStatus($filtros);
$porLoja        = Devolucao::getPorLoja($filtros);
$porFornecedor  = Devolucao::getPorFornecedor($filtros);
$evolucao       = Devolucao::getEvolucaoPorData($filtros);

$qtdDevolucoes  = $resumo['qtd'] ?? 0;
$totalCredito   = $resumo['soma'] ?? 0;
$qtdPendentes   = $resumoPendentes['qtd'] ?? 0;
$totalPendentes = $resumoPendentes['soma'] ?? 0;

$labelsStatus   = array_column($dadosStatus, 'status');
$valoresStatus  = array_column($dadosStatus, 'qtd');

$percentPendentes    = $qtdDevolucoes > 0 ? ($qtdPendentes / $qtdDevolucoes) * 100 : 0;
$percentConfirmados  = 100 - $percentPendentes;
?>

<div class="titulo-container">
    <h1>Dashboard de Devoluções</h1>
</div>

<div class="resumo-cards">
    <div class="card card1">
        <small>Qtde Devoluções</small>
        <h2><?= $qtdDevolucoes ?></h2>
    </div>
    <div class="card card2">
        <small>Crédito Fornecedor</small>
        <h2>R$ <?= number_format($totalCredito, 2, ',', '.') ?></h2>
    </div>
    <div class="card card3">
        <small>Pendentes (não pagos)</small>
        <h2><?= $qtdPendentes ?> | R$ <?= number_format($totalPendentes, 2, ',', '.') ?></h2>
    </div>
    <div class="card card4">
        <small>Porcentagem:</small>
        <h2>Pendentes: <?= number_format($percentPendentes, 1, ',', '.') ?>% /
            Confirmados: <?= number_format($percentConfirmados, 1, ',', '.') ?>%</h2>
    </div>
</div>

<section>
    <div class="dashboard-grid">
        <section>
            <h2>Status das Devoluções</h2>
            <canvas id="graficoPizza"></canvas>
        </section>

        <section>
            <h2>Quantidade por Loja</h2>
            <canvas id="graficoBarrasLoja"></canvas>
        </section>

        <section class="full-width">
            <h2>Valor Total por Loja</h2>
            <canvas id="graficoValorLoja"></canvas>
        </section>

        <section class="highcanvas">
            <h2>Valor Total por Fornecedor</h2>
            <canvas id="graficoValorFornecedor"></canvas>
        </section>

        <section>
            <h2>Evolução por Data</h2>
            <canvas id="graficoLinhaData"></canvas>
        </section>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const devolucoes = <?= json_encode($devolucoes) ?>;
    const labelsStatus = <?= json_encode($labelsStatus) ?>;
    const valoresStatus = <?= json_encode($valoresStatus) ?>;

    new Chart(document.getElementById('graficoPizza'), {
        type: 'pie',
        data: { labels: labelsStatus, datasets: [{ data: valoresStatus, backgroundColor: ['#f39c12','#e74c3c','#2ecc71','#2980b9','#8e44ad','#16a085'] }] },
        options: { responsive: true, plugins: { legend: { position: 'bottom' }, datalabels: { formatter: (v, ctx) => ctx.chart.data.labels[ctx.dataIndex] + ": " + v, color: '#fff', font: { weight: 'bold' } } } },
        plugins: [ChartDataLabels]
    });

    const lojasMap = {}, valorLojaMap = {};
    devolucoes.forEach(d => {
        lojasMap[d.loja] = (lojasMap[d.loja] || 0) + 1;
        valorLojaMap[d.loja] = (valorLojaMap[d.loja] || 0) + parseFloat(d.valor);
    });
    const lojas = Object.keys(lojasMap);
    const qtdPorLoja = Object.values(lojasMap);
    const valorPorLoja = Object.values(valorLojaMap);

    new Chart(document.getElementById('graficoBarrasLoja'), {
        type: 'bar',
        data: { labels: lojas, datasets: [{ data: qtdPorLoja, backgroundColor: '#3498db' }] },
        options: { plugins: { legend: { display: false }, datalabels: { anchor: 'end', align: 'top' } }, responsive: true }
    });

    new Chart(document.getElementById('graficoValorLoja'), {
        type: 'bar',
        data: { labels: lojas, datasets: [{ data: valorPorLoja, backgroundColor: '#2ecc71' }] },
        options: { plugins: { legend: { display: false }, datalabels: { anchor: 'end', align: 'top', formatter: v => "R$ " + v.toLocaleString("pt-BR") } }, responsive: true }
    });

    const fornecedorMap = {}, valorFornecedorMap = {};
    devolucoes.forEach(d => {
        fornecedorMap[d.fornecedor] = (fornecedorMap[d.fornecedor] || 0) + 1;
        valorFornecedorMap[d.fornecedor] = (valorFornecedorMap[d.fornecedor] || 0) + parseFloat(d.valor);
    });
    const fornecedores = Object.keys(fornecedorMap);
    const valorPorFornecedor = Object.values(valorFornecedorMap);

    new Chart(document.getElementById('graficoValorFornecedor'), {
        type: 'bar',
        data: { labels: fornecedores, datasets: [{ data: valorPorFornecedor, backgroundColor: '#9b59b6' }] },
        options: { indexAxis: 'y', plugins: { legend: { display: false }, datalabels: { anchor: 'end', align: 'right', formatter: v => "R$ " + v.toLocaleString("pt-BR") } }, responsive: true }
    });

    const dataMap = {};
    devolucoes.forEach(d => {
        const data = d.data_emissao.split(' ')[0];
        dataMap[data] = (dataMap[data] || 0) + parseFloat(d.valor);
    });
    const datas = Object.keys(dataMap).sort();
    const valorPorData = datas.map(d => dataMap[d]);

    new Chart(document.getElementById('graficoLinhaData'), {
        type: 'line',
        data: { labels: datas, datasets: [{ data: valorPorData, fill: true, borderColor: '#27ae60', backgroundColor: 'rgba(39,174,96,0.2)', tension: 0.3 }] },
        options: { plugins: { legend: { display: false } }, responsive: true }
    });
});
</script>