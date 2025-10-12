<?php

require_once("../assets/connection.php");

session_start();

$id_expedicao = null;
$dados_expedicao = [];
$mensagem = '';

$camposExcluidos = [
    'id',
    'flow',
];

$nomesAmigaveis = [
    'produto' => 'Produto',
    'transportadora' => 'Transportadora',
    'nomeMotorista' => 'Nome do Motorista',
    'data' => 'Data do Checklist',
    'placaCavalo' => 'Placa do Cavalo',
    'cnhMotorista' => 'CNH do Motorista',
    'horaEntrada' => 'Hora de Entrada',
    'placaTanque1' => 'Placa Tanque 1',
    'destino' => 'Destino',
    'responsavelBalanca' => 'Responsável Balança',
    'placaTanque2' => 'Placa Tanque 2',
    'volumeCarreta' => 'Volume da Carreta (Litros)',

    'obs' => 'Observações',

    'farois' => 'Faróis, lanternas e setas em bom estado?',
    'vagoes' => 'Vagões, válvulas e conexões isentos de vazamentos?',
    'cavalo' => 'No cavalo o painel de segurança e rótulo de risco meio ambiente estão conforme?',
    'extintores' => 'Os extintores estão com a validade da carga e teste hidrostático conformes?',
    'verificado' => 'Verificado se há volume remanescente nos compartimentos da carreta a ser carregada?',
    'lavar' => 'Necessidade de lavar e/ou secar o tanque? (*Secar a umidade, se presente)',
    'vedacao' => 'A vedação da boca de carregamento está conforme?',
    'valvula' => 'Possui válvula de fundo de fecho rápido?',
    'transporte' => 'Caminhão tanque em condições de realizar o transporte?',
    'tubos' => 'Certificado se os tubos de descargas (canos) irão ser carregados cheios ou vazio?',
    'carregamento' => 'Carregamento aprovado? Caso não, informar à supervisão.',
];

$gruposCampos = [
    'Dados Gerais do Carregamento' => [
        'data',
        'horaEntrada',
        'produto',
        'volumeCarreta',
        'destino',
        'obs'
    ],
    'Dados do Veículo e Motorista' => [
        'nomeMotorista',
        'cnhMotorista',
        'transportadora',
        'placaCavalo',
        'placaTanque1',
        'placaTanque2'
    ],
    'Itens de Checklist (Inspeção)' => [
        'farois',
        'vagoes',
        'cavalo',
        'extintores',
        'verificado',
        'lavar',
        'vedacao',
        'valvula',
        'transporte',
        'tubos',
        'carregamento'
    ],
    'Responsáveis' => [
        'responsavelBalanca',
        'responsavelExpedicao'
    ]
];

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_expedicao = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $sql_select = "SELECT id,flow,ticket,produto,transportadora,nomeMotorista,data,placaCavalo,cnhMotorista,horaEntrada,
                    placaTanque1,destino,responsavelBalanca,placaTanque2,volumeCarreta,farois,vagoes,cavalo,extintores,
                    verificado,lavar,vedacao,valvula,transporte,tubos,carregamento,responsavelExpedicao,laudo,baia,
                    temperaturaAmostra,densidade,temperaturaCarreta,lacresAmostra,lacreMotorista,lacresCarreta,obs
                   FROM db_checklist.dbo.tb_marking WHERE id = :id";

    try {
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->bindParam(':id', $id_expedicao, PDO::PARAM_INT);
        $stmt_select->execute();
        $dados_expedicao = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if (!$dados_expedicao) {
            $mensagem = "Checklist com ID " . htmlspecialchars($id_expedicao) . " não encontrado.";
        }
    } catch (PDOException $e) {
        $mensagem = "Erro ao carregar dados: " . $e->getMessage();
    }
} else {
    $mensagem = "ID do checklist não fornecido.";
}

function formatChecklistValue($key, $value, $nomesAmigaveis)
{
    $displayValue = '';
    $statusClass = '';

    if (($key === 'data' || $key === 'horaEntrada') && $value instanceof DateTime) {
        $displayValue = ($key === 'data') ? $value->format('d/m/Y') : $value->format('H:i');
    } else if (
        in_array($key, array_keys(array_intersect_key($nomesAmigaveis, array_flip([
            'farois',
            'vagoes',
            'cavalo',
            'extintores',
            'verificado',
            'lavar',
            'vedacao',
            'valvula',
            'transporte',
            'tubos',
            'carregamento'
        ]))))
    ) {
        $normalizedValue = strtolower($value);

        if ($normalizedValue == 'sim') {
            $displayValue = 'Sim';
            $statusClass = 'status-sim';
        } elseif ($normalizedValue == 'nao') {
            $displayValue = 'Não';
            $statusClass = 'status-nao';
        } elseif ($normalizedValue == 'na') {
            $displayValue = 'N/A';
            $statusClass = 'status-na';
        } else {
            $displayValue = 'Desconhecido';
            $statusClass = '';
        }
    } else {
        $displayValue = htmlspecialchars($value);
    }

    $label = isset($nomesAmigaveis[$key]) ? $nomesAmigaveis[$key] : ucwords(str_replace(['_', 'responsavel'], [' ', 'Resp. '], $key));

    return [
        'label' => $label,
        'displayValue' => ($displayValue !== '' ? $displayValue : '-'),
        'statusClass' => $statusClass
    ];
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Checklist</title>
    <link rel="stylesheet" href="../css/cssChecklist.css">
</head>

<body>
    <a href="#" onclick="history.back();" class="back-button">Voltar</a>
    <div class="container">
        <div class="header-green">
            <h3>CHECKLIST E AUTORIZAÇÃO DE EMBARQUE PARA CARREGAMENTO DE BIODIESEL</h3>
        </div>

        <?php if ($mensagem): ?>
            <div
                class="message-box <?php echo (strpos($mensagem, 'Erro') !== false || strpos($mensagem, 'não encontrado') !== false) ? 'error-message' : ''; ?>">
                <p><?php echo $mensagem; ?></p>
            </div>
        <?php elseif ($dados_expedicao): ?>
            <div class="info-bar">
                <div class="info-item"><strong>TICKET/CIRCULAÇÃO :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['ticket'] ?? '-'); ?></div>
                <div class="info-item"><strong>PRODUTO :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['produto'] ?? '-'); ?></div>
                <div class="info-item"><strong>LAUDO :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['laudo'] ?? '-'); ?></div>
                <div class="info-item"><strong>TRANSPORTADORA :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['transportadora'] ?? '-'); ?></div>
                <div class="info-item"><strong>NOME MOTORISTA :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['nomeMotorista'] ?? '-'); ?></div>
                <div class="info-item"><strong>CNH MOTORISTA :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['cnhMotorista'] ?? '-'); ?></div>
                <div class="info-item"><strong>DATA :</strong>
                    <?php echo (isset($dados_expedicao['data']) && $dados_expedicao['data'] != '') ? date('d/m/Y', strtotime($dados_expedicao['data'])) : '-'; ?>
                </div>
                <div class="info-item"><strong>HORA ENTRADA :</strong>
                    <?php echo (isset($dados_expedicao['horaEntrada']) && $dados_expedicao['horaEntrada'] != '') ? date('H:i', strtotime($dados_expedicao['horaEntrada'])) : '-'; ?>
                </div>
                <div class="info-item"><strong>DESTINO :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['destino'] ?? '-'); ?></div>
                <div class="info-item"><strong>PLACA DO CAVALO:</strong>
                    <?php echo htmlspecialchars($dados_expedicao['placaCavalo'] ?? '-'); ?></div>
                <div class="info-item"><strong>PLACA TANQUE 1 :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['placaTanque1'] ?? '-'); ?></div>
                <div class="info-item"><strong>PLACA TANQUE 2 :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['placaTanque2'] ?? '-'); ?></div>
                <div class="info-item"><strong>VOLUME CARRETA :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['volumeCarreta'] ?? '-'); ?></div>
                <div class="info-item"><strong>RESPONSÁVEL BALANÇA :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['responsavelBalanca'] ?? '-'); ?></div>
                <div class="info-item"><strong>RESPONSÁVEL EXPEDIÇÃO :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['responsavelExpedicao'] ?? '-'); ?></div>
            </div>

            <div class="checklist-section">
                <?php foreach ($nomesAmigaveis as $key => $pergunta): ?>
                    <?php
                    if (in_array($key, ['farois', 'vagoes', 'cavalo', 'extintores', 'verificado', 'lavar', 'vedacao', 'valvula', 'transporte', 'tubos', 'carregamento'])):
                        $currentValue = $dados_expedicao[$key] ?? '';
                        $normalizedValue = strtolower($currentValue);

                        $exibirValor = '';
                        $classeCor = '';

                        if ($normalizedValue == 'sim') {
                            $exibirValor = 'Sim';
                            $classeCor = 'status-sim';
                        } elseif ($normalizedValue == 'nao') {
                            $exibirValor = 'Não';
                            $classeCor = 'status-nao';
                        } elseif ($normalizedValue == 'na') {
                            $exibirValor = 'N/A';
                            $classeCor = 'status-na';
                        }
                    ?>
                        <div class="checklist-item">
                            <div class="question"><?php echo htmlspecialchars($pergunta); ?></div>
                            <div class="options">
                                <span class="<?php echo $classeCor; ?>"><?php echo $exibirValor; ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="info-bar2">
                <div class="info-item2"><strong>N° BAIA DE CARREGAMENTO/ BRAÇO:</strong>
                    <?php echo htmlspecialchars($dados_expedicao['baia'] ?? '-'); ?></div>
                <div class="info-item2"><strong>TEMPERATURA DA AMOSTRA (°C):</strong>
                    <?php echo htmlspecialchars($dados_expedicao['temperaturaAmostra'] ?? '-'); ?> °C</div>
                <div class="info-item2"><strong>DENSIDADE DA AMOSTRA:</strong>
                    <?php echo htmlspecialchars($dados_expedicao['densidade'] ?? '-'); ?> Kg/L</div>
                <div class="info-item2"><strong>TEMPERATURA DA CARRETA (°C):</strong>
                    <?php echo htmlspecialchars($dados_expedicao['temperaturaCarreta'] ?? '-'); ?> °C</div>
                <div class="info-item2"><strong>LACRES DAS AMOSTRAS:</strong>
                    <?php echo htmlspecialchars($dados_expedicao['lacresAmostra'] ?? '-'); ?></div>
                <div class="info-item2"><strong>LACRE DA AMOSTRA DO MOTORISTA:</strong>
                    <?php echo htmlspecialchars($dados_expedicao['lacreMotorista'] ?? '-'); ?></div>
                <div class="info-item2"><strong>LACRES DA CARRETA:</strong>
                    <?php echo htmlspecialchars($dados_expedicao['lacresCarreta'] ?? '-'); ?></div>
            </div>

            <div class="info-bar3" id="resultadoConversao">
                <h2>RESULTADO DA CONVERSÃO DOS DADOS</h2>
            </div>

            <div class="info-bar2">

                <div class="info-item2">
                    <strong><label for="massaCarreta">MASSA DA CARRETA (BALANÇA):</label>
                        <input class="valor-azul" type="number" id="massaCarreta" autocomplete="off" oninput="atualizarValores()"></strong>
                </div>
                <div class="info-item2"><strong>DENSIDADE 20°: </strong>
                    <span class="valor-vermelho"><span id="densidade"></span> Kg/L</span>
                </div>
                <div class="info-item2"><strong>FATOR CORREÇÃO: </strong>
                    <span class="valor-vermelho"><span id="fatorCorrecao"></span> a 20 °C</span>
                </div>
                <div class="info-item2"><strong>VOLUME CONVERTIDO:</strong>
                    <span class="valor-azul"><span id="volumeConvertido"></span> M³</span>
                </div>
                <div class="info-item2"><strong>VOLUME CONVERTIDO (BALANÇA):</strong>
                    <span class="valor-azul"><span id="volumeConvertidoBalanca"></span> M³</span>
                </div>
                <div class="info-item2"><strong>Δ VOLUME:</strong>
                    <span class="valor-azul"><span id="deltaVolume"></span> L</span>
                </div>
                <div class="info-item2"><strong>VALOR DE EMBARQUE:</strong>
                    <span class="valor-azul"><span id="valorEmbarque"></span> Kg</span>
                </div>

            </div>

            <div class="info-bar2">

            
            </div>


        <?php else: ?>
            <div class="message-box error-message">
                <p>Nenhum checklist encontrado para o ID fornecido ou ocorreu um erro.</p>
            </div>
        <?php endif; ?>
    </div>


    <script>
        // Usamos um objeto global para evitar poluir o escopo
        window.APP_CONFIG = window.APP_CONFIG || {};

        // Aqui o PHP imprime o valor da variável como código JS válido
        window.APP_CONFIG.volumeCarreta = <?php echo json_encode($dados_expedicao['volumeCarreta'] ?? '-'); ?>;
        window.APP_CONFIG.densidade = <?php echo json_encode($dados_expedicao['densidade'] ?? ' '); ?>;
        window.APP_CONFIG.temperaturaAmostra = <?php echo json_encode($dados_expedicao['temperaturaAmostra'] ?? ' '); ?>;
        window.APP_CONFIG.temperaturaCarreta = <?php echo json_encode($dados_expedicao['temperaturaCarreta'] ?? ' '); ?>;
    </script>
    <script src="../js/tabelaDensidade.js"></script>
    <script src="../js/conversaoDados.js"></script>

</body>

</html>
<?php $conn = null; ?>