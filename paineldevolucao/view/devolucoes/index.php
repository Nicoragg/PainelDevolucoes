<?php
require_once __DIR__ . "/../../models/Devolucao.php";
require_once __DIR__ . "/../../config/Database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['limpar'])) {
    unset($_SESSION['filtros_devolucoes']);
    $_GET = [];
}

if (!empty($_GET)) {
    $_SESSION['filtros_devolucoes'] = $_GET;
} elseif (!empty($_SESSION['filtros_devolucoes'])) {
    $_GET = $_SESSION['filtros_devolucoes'];
}

$filtroIdChamado   = $_GET['id_chamado'] ?? '';
$filtroFornecedor  = $_GET['fornecedor'] ?? '';
$filtroLoja        = $_GET['loja'] ?? '';
$filtroMotivo      = $_GET['motivo'] ?? '';
$filtroStatus      = $_GET['status'] ?? '';
$filtroPagamento   = $_GET['forma_pagamento'] ?? '';
$dataInicio        = $_GET['data_inicio'] ?? '';
$dataFim           = $_GET['data_fim'] ?? '';
$filtroResponsavel = $_GET['responsavel'] ?? '';

$itensPorPagina = $_GET['limite'] ?? 20;
$paginaAtual    = $_GET['pagina'] ?? 1;
$offset         = ($paginaAtual - 1) * $itensPorPagina;

$condicoes = [];
$params = [];

if ($filtroIdChamado) {
    $condicoes[] = "id_chamado = ?";
    $params[] = $filtroIdChamado;
}
if ($filtroFornecedor) {
    $condicoes[] = "fornecedor LIKE ?";
    $params[] = "%$filtroFornecedor%";
}
if ($filtroLoja) {
    $condicoes[] = "loja = ?";
    $params[] = $filtroLoja;
}
if ($filtroMotivo) {
    $condicoes[] = "motivo = ?";
    $params[] = $filtroMotivo;
}
if ($filtroStatus) {
    $condicoes[] = "status = ?";
    $params[] = $filtroStatus;
}
if ($filtroPagamento) {
    $condicoes[] = "forma_pagamento = ?";
    $params[] = $filtroPagamento;
}

if ($filtroResponsavel) {
    $condicoes[] = "responsavel LIKE ?";
    $params[] = "%$filtroResponsavel%";
}

if ($dataInicio && $dataFim) {
    $condicoes[] = "data_emissao BETWEEN ? AND ?";
    $params[] = $dataInicio;
    $params[] = $dataFim;
} elseif ($dataInicio) {
    $condicoes[] = "data_emissao >= ?";
    $params[] = $dataInicio;
} elseif ($dataFim) {
    $condicoes[] = "data_emissao <= ?";
    $params[] = $dataFim;
}



$where = $condicoes ? "WHERE " . implode(" AND ", $condicoes) : "";

$total = Devolucao::count($where, $params);
$devolucoes = Devolucao::paginate($where, $params, $itensPorPagina, $offset);
$totalPaginas = ceil($total / $itensPorPagina);

$sqlCount = "SELECT COUNT(*) as qtd, SUM(valor) as soma FROM devolucoes $where";
$pdo = Database::getConnection();
$stmt = $pdo->prepare($sqlCount);
$stmt->execute($params);
$resumo = $stmt->fetch(PDO::FETCH_ASSOC);

$qtdDevolucoes  = $resumo['qtd'] ?? 0;
$totalCredito   = $resumo['soma'] ?? 0;

$sqlPendente = "SELECT COUNT(*) as qtd, SUM(valor) as soma 
                FROM devolucoes 
                $where " . ($where ? " AND " : "WHERE ") . " status != 'PROCESSO FINALIZADO - CRÉDITO CONFIRMADO'";
$stmt2 = $pdo->prepare($sqlPendente);                
$stmt2->execute($params);
$resumoPendentes = $stmt2->fetch(PDO::FETCH_ASSOC);

$sqlTempo = "SELECT AVG(DATEDIFF(data_finalizacao, data_abertura)) as media 
             FROM devolucoes 
             $where " . ($where ? " AND " : "WHERE ") . " data_abertura IS NOT NULL AND data_finalizacao IS NOT NULL";

$stmt3 = $pdo->prepare($sqlTempo);
$stmt3->execute($params);
$resumoTempo = $stmt3->fetch(PDO::FETCH_ASSOC);

$tempoMedio = $resumoTempo['media'] ? round($resumoTempo['media'], 1) : 0;

$qtdPendentes  = $resumoPendentes['qtd'] ?? 0;
$totalPendentes = $resumoPendentes['soma'] ?? 0;

$percentPendentes = $qtdDevolucoes > 0 ? ($qtdPendentes / $qtdDevolucoes) * 100 : 0;
$percentConfirmados = 100 - $percentPendentes;

$statuses = [
    'EM ANÁLISE',
    'PROCESSO FINALIZADO - CRÉDITO CONFIRMADO',
    'ERRO DE PROCEDIMENTO - GERAR VALE LOJA',
    'AGUARDANDO COLETA',
    'AGUARDANDO PAGAMENTO',
    'NEGADO FORNECEDOR',
    'PROCESSO FINALIZADO - SEM PAGAMENTO'
];

$formasPagamento = [
    '-',
    'VIA DEPÓSITO',
    'ABATIMENTO EM BOLETO'
];

$fornecedores = [
        "LETSKUK COMÉRCIO DE ALIMENTOS EIRELI",
        "SPAL INDUSTRIA DE BEBIDAS SA",
        "A W FABER CASTELL S A",
        "BBR IMPORTAÇÃO E EXPORTAÇÃO LTDA",
        "ABBOCATO IND E COM DE COSMETICOS LTDA",
        "ABC VENDAS REPRESENTAÇÕES LTDA",
        "ACRILEX TINTAS ESPECIAIS SA",
        "ADIBE E CASTRO CIMED DISTR.",
        "AGUIA SUL DISTRIBUIDORA LTDA",
        "AGUIAR NERI E MORAIS NERI",
        "ALL LISS LTDA",
        "ALMEIDA ATACADO LTDA",
        "ANB - PHARMA LOG DISTRIBUIÇÃO",
        "ARCON",
        "ARROJITO",
        "AVR DISTRIBUIDORA DE COSMÉTICOS LTDA",
        "AZ - BEAUTY LTDA",
        "AZULOS COSMÉTICOS LTDA",
        "BACKER E BACKER LTDA",
        "BALOJA CONCEITO COM DIST DE COSM LTDA",
        "BEAUTYOU COSMÉTICOS LTDA",
        "BETTANIN S A",
        "BF DISTRIBUIDORA",
        "BIMBO DO BRASIL",
        "BIO ATIVOS - BIO EXTRATUS",
        "BIO COSMETICOS WEBER LTDA",
        "BIO IDEAL - FLORAL THERAPI",
        "BLOOM COSMETICOS",
        "BLUMED",
        "BORDELLO COMERCIO DE ALIMENTOS PARA ANIMAIS LTDA",
        "BRASIL SUL COMERCIO",
        "BRAZMIX DISTRIBUIDORA",
        "BUENO COSMETICOS LTDA",
        "C COLLETTI E CIA LTDA",
        "CABLE BOX ELETRONICA LTDA",
        "CAFE TRES CORAÇÕES S A",
        "CARLOS DE SOUZA - COMERCIO DE PRODUTOS DE BELEZA",
        "CAROL BEAUTY COSMETICOS LTDA - ME",
        "CBN",
        "CEGEMED DIST. PRODUTOS FARMACEUTICOS LTDA",
        "CIMED e CO S.A.",
        "CINCO LTDA",
        "CIRANDA MAGICA LTDA",
        "CLASAN DISTRIBUIDORA LTDA",
        "CLUBE DA MEIA",
        "COM TINTAS E MAT ELETRIC E HIDVERGINIA LTDA LJ 28",
        "CROMUS EMBALAGENS INDUSTRIA E COMERCIO LTDA",
        "CS BEAUTY COSMETICOS LTDA",
        "DALUBABY DIST LTDA",
        "DESTRO COMERCIAL LTDA",
        "DEYCON",
        "DICOPAR",
        "DIMEBRAS",
        "DIMED S/A DISTR DE MEDICAMENTOS",
        "DISTRIBUIDORA MODENUTI SP",
        "DISTRILOBO",
        "DISTRIMAX",
        "DM PARANA",
        "DP4",
        "DROGA CENTER DISTRIBUIDORA",
        "DWG DISTRIBUIDORA DE COSMETICOS LTDA",
        "E-UB COMERCIO LTDA",
        "EF ROCHA DISTRIBUIDORA",
        "EISEN DISTRIBUIÇÃO ESPECIALIZADA",
        "ESMERALDA LTDA",
        "EXCLUSIVA DIST COSMETICOS LTDA",
        "F CHICHINELI",
        "FERRERO COSMETICOS",
        "FF PRODUTOS",
        "FFE E BARROS DISTRIBUIDORA LTDA",
        "FLEX SUL CALÇADOS",
        "FSA COMERCIO E REPRESENTAÇÕES",
        "GAZOLLA & GAZOLLA COMERCIAL",
        "GENESIO MENDES DISTRIBUIDORA",
        "GERMED",
        "GETH DISTRIBUIDORA",
        "GIRA BRASIL DISTRIBUIDORA",
        "GIRA BRASIL MEDICAMENTOS",
        "GO FARMA COMERCIAL FARMACEUTICA LTDA - DISLAB",
        "GUILHERME NUNES DE CARVALHO - ME",
        "HD UBER",
        "HI TECNOLOGIES",
        "HUBER DISTRIBUIDORA DE ALIMENTOS LTDA",
        "IMBECOR PRODUTOS DE BELEZA LTDA",
        "JJT IMPORTAÇÃO LTDA",
        "JKM",
        "JMS DISTRIBUIDORA",
        "KISS NEW YORK IMBECOR PRODUTOS DE BELEZA LTDA",
        "KLEY HERTZ",
        "LABOTRAT DISTRIBUIDORA DE COSMÉTICOS LTDA",
        "LDN DISTRIBUIDORA DE PERFUMARIA LTDA",
        "LEMES E OLIVEIRA - PILHAS",
        "LOGIKA DISTRIBUIDORA DE COSMETICOS",
        "MACRO COMERCIO INTERNACIONAL",
        "MAIORKA",
        "MARCIA JEANE RAMOS DE ALMEIDA LTDA",
        "MARTINS COMERCIO E SERVIÇOS DE DISTRIBUIÇÃO S.A",
        "MASI - MCS COSMETICS",
        "MASTRO DISTRIBUIDORA LTDA",
        "MEDCHAP DISTRIBUIDORA DE MEDICAMENTOS CHAPECO",
        "MEDICAMENTAL DISTRIBUIDORA",
        "MEGA BURTS COMERCIAL LTDA",
        "MEGA TEN",
        "MELLODIA DISTRIBUIDORA LTDA",
        "MIKONOS",
        "MILI S.A",
        "MJR DIST E COM DE COSM LTDA",
        "MLD LTDA",
        "MONDIAL",
        "MULTIFOODS ALIMENTOS LTDA",
        "NADER COMERCIO DE COSMETICOS LTDA",
        "NAVARRO DISTRIBUIDORA DE MEDICAMENTOS S/A",
        "NDS - DISTRIBUIÇÃO",
        "NEOBRAS DISTRIBUIÇÃO",
        "NESTLE BRASIL LTDA",
        "NOHRAAN DISTRIBUIDORA LTDA",
        "NOVA ESSENCIA COSMETICOS",
        "NOVA GERAÇÂO LTDA",
        "NOVAMED",
        "NS DISTRIBUIÇÃO LTDA",
        "NUTRIPORT COMERCIAL LTDA",
        "ODRES",
        "OFIR ALIMENTOS",
        "ONIZ DIST LTDA",
        "ORALPROX SAUDE E BELEZA LTDA",
        "PANPHARMA MEDICAMENTOS",
        "PDV",
        "PENIEL",
        "PEPSICO DISTRIBUIDORA ALIMENTICIA",
        "PH DISTRIBUIDORA COSMETICOS LTDA",
        "POLI TODESCO COMERCIO DE PRODUTOS ALIMENTICIOS",
        "PRINCIPIA PR COMERCIO DE COSMETICOS",
        "PROFARMA DIST.",
        "PWD",
        "QUALYBLESS DO BRASIL",
        "RBY - COSMETICOS",
        "REGIAMAR PRODUTOS DE BELEZA LTDA",
        "REVAL DISTRIBUIÇÃO",
        "RIKA DISTRIBUIDORA DE ALIMENTOS EIRELI",
        "RM COSMETICOS",
        "RMS DISTRIBUIDORA DE COSMETICOS LTDA",
        "ROGE",
        "ROMANHA INDUSTRIA DE ALIMENTOS LTDA",
        "RUBY ROSE-PR LTDA",
        "SANTA CRUZ DISTRIBUIDORA",
        "SERVIMED MEDICAMENTOS",
        "SL SILVA - ZALIKE",
        "SOAN COMERCIO E DISTRIBUIÇÃO LTDA",
        "STAMPA",
        "STY COMERCIO DE COSMETICOS LTDA",
        "SUPLEY SUPLEMENTOS LTDA",
        "TOALITAS BRASIL LTDA",
        "TRIUNFANTE",
        "UNION MEDIC REPRESENTAÇÕES DE PRODUTOS PARA SAUDE LTDA",
        "VELOMED DISTRIBUIDORA DE ALIMENTOS SAUDAVEIS",
        "VILLE COMERCIO DE COSMETICOS - EIRELLI",
        "VINISUL DISTRIBUIDORA DE ALIMENTOS LTDA",
        "VISION MEDICAMENTOS",
        "VITAO ALIMENTOS LTDA",
        "VONDER IMPORT",
        "WERBRAN",
        "WERDUNBRASIL",
        "WM",
        "ZONACRIATIVA"
    ];

    
?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const btnFiltro = document.getElementById("toggleFiltro");
    const modal = document.getElementById("filtrosModal");
    const fechar = document.getElementById("fecharFiltro");
    const formFiltro = document.getElementById("filtrosForm");

    btnFiltro.addEventListener("click", () => {
        modal.style.display = "block";
    });

    fechar.addEventListener("click", () => {
        modal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });

    formFiltro.addEventListener("submit", () => {
        modal.style.display = "none";
    });
});
</script>

<div class="titulo-container">
    <h1>Lista de Devoluções</h1>
    <button id="toggleFiltro">Filtrar</button>
</div>

<div id="filtrosModal" class="modal">
  <div class="modal-content">
    <span class="fechar" id="fecharFiltro">&times;</span>
    <h3>Filtros</h3>

<form method="GET" id="filtrosForm">
    <label>ID Chamado:
        <input type="number" name="id_chamado" value="<?= htmlspecialchars($filtroIdChamado) ?>">
    </label>

    <label>Fornecedor:
        <input type="text" name="fornecedor" list="lista-fornecedores" 
               value="<?= htmlspecialchars($filtroFornecedor) ?>">
    </label>
    <datalist id="lista-fornecedores">
        <?php foreach ($fornecedores as $f): ?>
            <option value="<?= htmlspecialchars($f) ?>"></option>
        <?php endforeach; ?>
    </datalist>

    <label>Responsável:
        <input type="text" name="responsavel" list="lista-responsaveis" 
            value="<?= htmlspecialchars($filtroResponsavel) ?>">
    </label>
    <datalist id="lista-responsaveis">
        <?php 
        $stmtResp = $pdo->query("SELECT DISTINCT responsavel FROM devolucoes WHERE responsavel IS NOT NULL AND responsavel != '' ORDER BY responsavel");
        while ($r = $stmtResp->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='".htmlspecialchars($r['responsavel'])."'></option>";
        }
        ?>
    </datalist>

    <label>Loja:
        <input type="text" name="loja" list="lista-lojas" 
               value="<?= htmlspecialchars($filtroLoja) ?>">
    </label>
    <datalist id="lista-lojas">
        <?php $lojas = [
            'Loja 01','Loja 03','Loja 04','Loja 05','Loja 06','Loja 07','Loja 08','Loja 09','Loja 10',
            'Loja 12','Loja 13','Loja 14','Loja 15','Loja 16','Loja 19','Loja 20','Loja 21','Loja 23',
            'Loja 25','Loja 26','Loja 27','Loja 28','Loja 31','Loja 32','Loja 33','Loja 34','Loja 35',
            'Loja 36','Loja 37','Loja 38','Loja 39','Loja 40','Loja 41','Loja 43','Loja 44','Loja 45',
            'Loja 46','Loja 47','Loja 48','Loja 49','Loja 50','Loja 51','Loja 52','Loja 53','Loja 54',
            'Loja 55','Loja 56','Loja 57','Loja 58','Loja 59','Loja 60','Loja 61','Loja 62','Loja 63',
            'Loja 64','Loja 65','Loja 66','Loja 67','Loja 68','Loja 69','Loja 70','Loja 72','Loja 73',
            'Loja 74','Loja 75','Loja 76','Loja 77','Loja 78','Loja 79','Loja 80'
        ];
        foreach ($lojas as $l) { echo "<option value=\"" . htmlspecialchars($l) . "\"></option>";
        }
        ?>
    </datalist>

    <label>Motivo:
        <select name="motivo">
            <option value="">Todos</option>
            <?php foreach(['AVARIA','FALTA','SOBRA','VENCIDOS','DESVIO DE QUALIDADE','POLITICA DE TROCA','PRÉ VENCIDO','DIVERGÊNCIA PEDIDO DE COMPRAS'] as $m): ?>
                <option value="<?= $m ?>" <?= $filtroMotivo===$m?'selected':'' ?>><?= $m ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Status:
        <select name="status">
            <option value="">Todos</option>
            <?php foreach($statuses as $s): ?>
                <option value="<?= $s ?>" <?= $filtroStatus===$s?'selected':'' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Forma de Pagamento:
        <select name="forma_pagamento">
            <option value="">Todos</option>
            <?php foreach($formasPagamento as $fp): ?>
                <option value="<?= $fp ?>" <?= $filtroPagamento===$fp?'selected':'' ?>><?= $fp ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Período:
        <input type="date" name="data_inicio" value="<?= htmlspecialchars($dataInicio) ?>">
        até
        <input type="date" name="data_fim" value="<?= htmlspecialchars($dataFim) ?>">
    </label>

    <label>Linhas por página:
        <select name="limite">
            <?php foreach([10,30,50,100,150,200] as $n): ?>
                <option value="<?= $n ?>" <?= $itensPorPagina==$n?'selected':'' ?>><?= $n ?></option>
            <?php endforeach; ?>
        </select>
    </label>

        <button type="submit">Aplicar</button>

</form>
  </div>
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
        <h2>Pendentes: <?= number_format($percentPendentes, 1, ',', '.') ?>% <br>
            Confirmados: <?= number_format($percentConfirmados, 1, ',', '.') ?>%</h2>
    </div>

    <div class="card card5">
        <small>Tempo médio de conclusão:</small>
        <h2><?= $tempoMedio > 0 ? $tempoMedio . ' dias' : '-' ?></h2>
    </div>
</div>

<section class="tabela-container">

<?php if(isset($_SESSION['success'])): ?>
    <div class="mensagem sucesso"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<table class="tabela-devolucao">
    <thead>
        <tr>
            <th>ID Chamado</th>
            <th>Data Abertura</th>
            <th>Fornecedor</th>
            <th>Loja</th>
            <th>NF Origem</th>
            <th>NF Devolução</th>
            <th>Valor NF</th>
            <th>Data Emissão</th>
            <th>Data Crédito</th>
            <th>Data de Coleta</th>
            <th>Forma Pagamento</th>
            <th>Responsável</th>
            <th>Status</th>
            <th>Motivo</th>
            <th>Observação</th>
            <th>Comprovante</th>
            <?php if ($isAdmin == 1): ?>
                <th>Ações</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach($devolucoes as $d): ?>
        <tr>
            <td><?= htmlspecialchars($d['id_chamado']); ?></td>
            <td><?= $d['data_abertura'] ? date('d/m/Y', strtotime($d['data_abertura'])) : '-'; ?></td>
            <td><?= htmlspecialchars($d['fornecedor']); ?></td>
            <td><?= htmlspecialchars($d['loja']); ?></td>
            <td><?= htmlspecialchars($d['nf_origem']); ?></td>
            <td><?= htmlspecialchars($d['nf']); ?></td>
            <td>R$<?= number_format($d['valor'], 2, ',', '.'); ?></td>
            <td><?= date('d/m/Y', strtotime($d['data_emissao'])); ?></td>
            <td><?= $d['data_recebimento'] ? date('d/m/Y', strtotime($d['data_recebimento'])) : '-'; ?></td>
            <td><?= $d['data_finalizacao'] ? date('d/m/Y', strtotime($d['data_finalizacao'])) : '-'; ?></td>
            <td><?= htmlspecialchars($d['forma_pagamento']); ?></td>
            <td><?= htmlspecialchars($d['responsavel']); ?></td>
            <td><?= htmlspecialchars($d['status']); ?></td>
            <td><?= htmlspecialchars($d['motivo']); ?></td>
            <td><?= htmlspecialchars($d['observacao']); ?></td>
            <td>
                <?php if ($d['comprovante']): ?>
                    <a href="uploads/<?= htmlspecialchars($d['comprovante']); ?>" target="_blank">Ver</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <?php if ($isAdmin == 1): ?>
                
                <td>
                    <div class="acoes">
                        <a href="index.php?page=devolucoes/edit&id=<?= $d['id']; ?>" class="btn editar">Editar</a>
                        <a href="index.php?page=devolucoes/delete&id=<?= $d['id']; ?>" 
                        class="btn excluir"
                        onclick="return confirm('Deseja excluir?')">Excluir</a>
                    </div>
                </td>
            
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="paginacao">
    Páginas: 
    <?php for($i=1; $i <= $totalPaginas; $i++): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['pagina'=>$i])) ?>"
           class="btn <?= $i==$paginaAtual?'ativo':'' ?>">
           <?= $i ?>
        </a>
    <?php endfor; ?>
</div>
</section>