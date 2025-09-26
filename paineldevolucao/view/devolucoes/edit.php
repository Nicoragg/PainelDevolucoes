<?php
require_once __DIR__ . "/../../models/Devolucao.php";
$id = $_GET['id'] ?? null;
$devolucao = $id ? Devolucao::find($id) : null;
if (!$devolucao) {
    echo "<p>Devolução não encontrada.</p>";
    return;
}
?>

<div class="titulo-container">
    <h1>Editar Devolução</h1>
</div>
<section>
<form method="post" action="controllers/DevolucaoController.php?action=edit" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $devolucao['id']; ?>">

    <label for="id_chamado">ID do Chamado</label>
    <input type="text" id="id_chamado" name="id_chamado" 
           value="<?= htmlspecialchars($devolucao['id_chamado']); ?>">

    <label for="data_abertura">Data de Abertura do Chamado</label>
    <input type="date" id="data_abertura" name="data_abertura" 
       value="<?= htmlspecialchars($devolucao['data_abertura'] ?? ''); ?>">

    <label for="fornecedor">Fornecedor</label>
    <input type="text" id="fornecedor" name="fornecedor" value="<?= htmlspecialchars($devolucao['fornecedor']); ?>" required list="lista-fornecedores">

    <datalist id="lista-fornecedores">
    <?php $fornecedores = [
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
    foreach ($fornecedores as $f) { echo "<option value=\"" . htmlspecialchars($f) . "\"></option>";
    }
    ?>
    </datalist>

    
    <label for="loja">Loja</label>
    <input type="text" id="loja" name="loja" required value="<?= htmlspecialchars($devolucao['loja']); ?>" list="lista-lojas" >
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

    <label for="nf_origem">N° NF de Origem</label>
    <input type="text" id="nf_origem" name="nf_origem" 
        value="<?= htmlspecialchars($devolucao['nf_origem'] ?? ''); ?>">

    <label for="nf">N° NF Devolução</label>
    <input type="text" id="nf" name="nf" 
           value="<?= htmlspecialchars($devolucao['nf']); ?>">



    <label for="valor">Valor Nota Fiscal</label>
    <input type="text" id="valor" name="valor"
        value="<?= isset($devolucao['valor']) ? number_format($devolucao['valor'], 2, ',', '.') : '' ?>"
        oninput="formatarMoeda(this)">

    <script>
        function formatarMoeda(element) {
            let valor = element.value.replace(/\D/g, "");
            valor = (valor/100).toFixed(2) + "";
            valor = valor.replace(".", ",");
            valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            element.value = "R$ " + valor;
        }
    </script>

    <label for="data_emissao">Data Emissão</label>
    <input type="date" id="data_emissao" name="data_emissao" 
           value="<?= $devolucao['data_emissao']; ?>">

    <label for="data_recebimento">Data Crédito</label>
    <input type="date" id="data_recebimento" name="data_recebimento" 
           value="<?= $devolucao['data_recebimento']; ?>">

    <label for="data_finalizacao">Data de Coleta</label>
    <input type="date" id="data_finalizacao" name="data_finalizacao" 
           value="<?= $devolucao['data_finalizacao']; ?>">

    <label for="forma_pagamento">Forma de Pagamento</label>
    <select id="forma_pagamento" name="forma_pagamento" required>
        <option value="-" <?= ($devolucao['forma_pagamento'] ?? '') === '-' ? 'selected' : '' ?>>-</option>
        <option value="VIA DEPÓSITO" <?= ($devolucao['forma_pagamento'] ?? '') === 'VIA DEPÓSITO' ? 'selected' : '' ?>>VIA DEPÓSITO</option>
        <option value="ABATIMENTO EM BOLETO" <?= ($devolucao['forma_pagamento'] ?? '') === 'ABATIMENTO EM BOLETO' ? 'selected' : '' ?>>ABATIMENTO EM BOLETO</option>
    </select>

    <label for="responsavel">Responsável</label>
    <input type="text" id="responsavel" name="responsavel" 
           value="<?= htmlspecialchars($devolucao['responsavel']); ?>">

    <label for="status">Status</label>
    <select id="status" name="status" required>
        <?php
        $statuses = [
            'EM ANÁLISE',
            'PROCESSO FINALIZADO - CRÉDITO CONFIRMADO',
            'ERRO DE PROCEDIMENTO - GERAR VALE LOJA',
            'AGUARDANDO COLETA',
            'AGUARDANDO PAGAMENTO',
            'NEGADO FORNECEDOR',
            'PROCESSO FINALIZADO - SEM PAGAMENTO'
        ];
        foreach($statuses as $s) {
            $sel = $devolucao['status'] === $s ? 'selected' : '';
            echo "<option value='$s' $sel>$s</option>";
        }
        ?>
    </select>

    <label for="motivo">Motivo</label>
    <select id="motivo" name="motivo" required>
        <?php
        $motivos = [
            'AVARIA',
            'FALTA',
            'SOBRA',
            'VENCIDOS',
            'DESVIO DE QUALIDADE',
            'POLITICA DE TROCA',
            'PRÉ VENCIDO',
            'DIVERGÊNCIA PEDIDO DE COMPRAS'
        ];
        foreach($motivos as $m) {
            $sel = $devolucao['motivo'] === $m ? 'selected' : '';
            echo "<option value='$m' $sel>$m</option>";
        }
        ?>
    </select>

    <label for="observacao">Observação</label>
    <textarea id="observacao" name="observacao"><?= htmlspecialchars($devolucao['observacao'] ?? ''); ?></textarea>

    <label for="comprovante">Comprovante (PDF ou Imagem)</label>
    <input type="hidden" name="comprovante_atual" value="<?= htmlspecialchars($devolucao['comprovante']); ?>">
    <input type="file" id="comprovante" name="comprovante" accept="image/*,application/pdf">

    <?php if (!empty($devolucao['comprovante'])): ?>
        <p>Arquivo atual: 
            <a href="uploads/<?= htmlspecialchars($devolucao['comprovante']); ?>" target="_blank">
                Visualizar comprovante
            </a>
        </p>
    <?php endif; ?>

    <button type="submit">Atualizar</button>
</form>
</section>
