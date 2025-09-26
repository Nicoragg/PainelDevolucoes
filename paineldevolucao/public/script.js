 
src="https://cdn.jsdelivr.net/npm/chart.js"

$(document).ready(function(){
    $('#valor').inputmask('currency', {
        prefix: 'R$ ',
        groupSeparator: '.',
        radixPoint: ',',
        digits: 2,
        autoGroup: true,
        rightAlign: false
    });
});
let chartStatus, chartMotivo, chartFornecedor;

async function carregarDashboardComFiltros() {
    const params = new URLSearchParams({
        trimestre: document.getElementById('filtroTrimestre').value,
        fornecedor: document.getElementById('filtroFornecedor').value,
        status: document.getElementById('filtroStatus').value,
        motivo: document.getElementById('filtroMotivo').value
    });

    const res = await fetch(`api/dashboard.php?${params}`);
    const data = await res.json();

    document.getElementById('qtdDevolucoes').textContent = data.total_devolucoes;
    document.getElementById('creditoFornecedor').textContent = data.valor_total;

    atualizarGrafico(chartStatus, data.por_status);
    atualizarGrafico(chartMotivo, data.por_motivo);
    atualizarGrafico(chartFornecedor, data.por_fornecedor);
}

function atualizarGrafico(chart, dados) {
    chart.data.labels = Object.keys(dados);
    chart.data.datasets[0].data = Object.values(dados);
    chart.update();
}

function gerarGrafico(id, titulo) {
    const ctx = document.getElementById(id).getContext('2d');
    return new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [],
            datasets: [{
                label: titulo,
                data: [],
                backgroundColor: [
                    '#60a5fa', '#34d399', '#f87171', '#fbbf24',
                    '#a78bfa', '#fb923c', '#4ade80', '#facc15',
                    '#818cf8', '#a3e635'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: titulo }
            }
        }
    });
}

async function carregarOpcoesFiltros() {
    const res = await fetch('api/opcoes_filtros.php'); // você criará esse endpoint depois
    const data = await res.json();

    preencherSelect('filtroFornecedor', data.fornecedores);
    preencherSelect('filtroStatus', data.status);
    preencherSelect('filtroMotivo', data.motivos);
}

function preencherSelect(id, opcoes) {
    const select = document.getElementById(id);
    opcoes.forEach(op => {
        const option = document.createElement('option');
        option.value = op;
        option.textContent = op;
        select.appendChild(option);
    });
}

document.addEventListener('DOMContentLoaded', async () => {
    chartStatus = gerarGrafico('chartStatus', 'Devoluções por Status');
    chartMotivo = gerarGrafico('chartMotivo', 'Devoluções por Motivo');
    chartFornecedor = gerarGrafico('chartFornecedor', 'Devoluções por Fornecedor');

    await carregarOpcoesFiltros();
    await carregarDashboardComFiltros();

    document.querySelectorAll('.top-filters select').forEach(select => {
        select.addEventListener('change', carregarDashboardComFiltros);
    });
});