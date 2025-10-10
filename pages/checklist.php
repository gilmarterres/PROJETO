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
    'circulacao' => 'Número de Circulação',
    'produto' => 'Produto',
    'transportadora' => 'Transportadora',
    'nomeMotorista' => 'Nome do Motorista',
    'data' => 'Data do Checklist',
    'placaCarreta' => 'Placa da Carreta',
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
        'circulacao',
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
        'placaCarreta',
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

    $sql_select = "SELECT id,flow,ticket,circulacao,produto,transportadora,nomeMotorista,data,placaCarreta,cnhMotorista,horaEntrada,
                    placaTanque1,destino,responsavelBalanca,placaTanque2,volumeCarreta,farois,vagoes,cavalo,extintores,
                    verificado,lavar,vedacao,valvula,transporte,tubos,carregamento,responsavelExpedicao,laudo,baia,
                    temperaturaAmostra,densidade,vCarregado,temperaturaCarreta,lacresAmostra,lacreMotorista,lacresCarreta,obs
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
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            color: #333;
            line-height: 1.6;
            background-color: #ccc;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 0 auto;
        }

        .header-green {
            background-color: #5cb85c;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            margin-bottom: 10px;
        }

        h1 {
            margin: 0;
            font-weight: normal;
            font-size: 1.8em;
            color: #fff;
        }

        .btn-voltar {
            display: inline-block;
            background-color: #f0ad4e;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
        }

        .btn-voltar:hover {
            background-color: #ec971f;
        }

        .info-bar {
            background-color: #f0f8f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            border: 1px solid #d2e6d2;
        }

        .info-item {
            flex: 1 1 270px;
            font-size: 0.9em;
        }

        .info-item strong {
            font-weight: bold;
            margin-right: 5px;
        }

        .info-bar2 {
            background-color: #f0f8f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            border: 1px solid #d2e6d2;
        }

        .info-item2 {
            flex: 1 1 370px;
            font-size: 0.9em;
        }

        .info-item2 strong {
            font-weight: bold;
            margin-right: 5px;
        }

.info-bar3 {
    background-color: #ff0000ff;
    border-radius: 5px;
    margin-bottom: 20px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    border: 1px solid #d2e6d2;
    /* Adicionamos 'justify-content: center' aqui para centralizar o H2
       horizontalmente dentro do container flex, caso ele não tenha 'width: 100%' */
    justify-content: center;
}

.info-bar3 h2 {
    color: #ffffffff;
    /* Propriedade chave: centraliza o texto dentro do <h2> */
    text-align: center;
    /* Garante que o H2 ocupe toda a largura disponível para que a centralização
       funcione perfeitamente (útil em containers flex) */
    width: 100%; 
    margin: 10px;
}

.info-item3 {
    flex: 1 1 370px;
    font-size: 0.9em;
}

        .info-item3 strong {
            font-weight: bold;
            margin-right: 5px;
        }

        .checklist-section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;

            /*background-image: url('../logo.png');  Substitua pelo caminho da sua imagem */
            /* background-repeat: no-repeat; A imagem não se repetirá */
            background-position: center center; /* Centraliza a imagem no meio da página */
            background-attachment: fixed; /* A marca d'água permanece fixa ao rolar a página */
            background-size: 70%; /* Ajuste o tamanho da imagem (ex: 70% da largura do body) */
            /*opacity: 0.15;  Define a opacidade para a imagem (15% visível). Ajuste conforme necessário */
            /* ATENÇÃO: Aplicar opacidade diretamente ao body pode afetar todo o conteúdo.
            Melhor usar um pseudo-elemento ou uma div de sobreposição para a imagem.
            Veja o Cenário 2/3. */
        }

        .checklist-section h2 {
            color: #555;
            font-size: 1.2em;
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #ccc;
        }

        .checklist-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
        }

        .checklist-item:last-child {
            border-bottom: none;
        }

        .question {
            flex-grow: 1;
            margin-right: 15px;
            font-size: 0.95em;
            padding-right: 10px;
        }

        .options {
            flex-shrink: 0;
            display: flex;
            gap: 15px;
            font-size: 0.9em;
        }

        .options input[type="radio"] {
            margin-right: 5px;
        }

        .status-sim {
            color: #27ae60;
            font-weight: bold;
        }

        .status-nao {
            color: #c0392b;
            font-weight: bold;
        }

        .status-na {
            color: #3498db;
            font-weight: bold;
        }

        .input-group {
            margin-bottom: 15px;
            font-size: 0.9em;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .input-group input[type="text"],
        .input-group textarea {
            width: calc(100% - 18px);
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
            background-color: #eee;
        }

        .input-group textarea {
            resize: vertical;
            min-height: 60px;
        }

        .message-box {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .info-bar {
                flex-direction: column;
                gap: 10px;
            }

            .info-item {
                flex: 1 1 100%;
            }

            .checklist-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .options {
                margin-top: 5px;
            }

            .input-group input,
            .input-group textarea {
                width: 100%;
            }
        }

        .back-button{
    background-color: #4caf50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: background-color 0.3s ease;
    height: auto;
}

.back-button:hover{
    background-color: #45a049;
}

.back-button:active{
    background-color: #3e8e41;
    transform: translateY(1px);
}


.form-container {
    display: flex;
    flex-wrap: wrap; /* Isso permite que os elementos quebrem para a próxima linha */
    gap: 20px; /* Adiciona um espaço entre as colunas */
    margin-top: 20px;
    margin-bottom: 20px;
}

.form-group{
    /* flex-basis: calc(30% - 10px); 50% para duas colunas, menos metade do gap */
    /* min-width: 250px; */
}

.valor-azul{
     /* Define a cor azul */
    color: blue; 
    /* Garante que o texto está em negrito (caso não use <strong>) */
    font-weight: bold; 
}

.valor-vermelho{
     /* Define a cor azul */
    color: red; 
    /* Garante que o texto está em negrito (caso não use <strong>) */
    font-weight: bold; 
}

#massaCarreta{
    width: 100px;
}


    </style>
</head>

<body>
    <a href="#" onclick="history.back();" class="back-button">Voltar</a>
    <div class="container">
        <div class="header-green">
            <h1>CHECKLIST</h1>
        </div>

        <?php if ($mensagem): ?>
            <div
                class="message-box <?php echo (strpos($mensagem, 'Erro') !== false || strpos($mensagem, 'não encontrado') !== false) ? 'error-message' : ''; ?>">
                <p><?php echo $mensagem; ?></p>
            </div>
        <?php elseif ($dados_expedicao): ?>
            <div class="info-bar">
                <div class="info-item"><strong>TICKET :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['ticket'] ?? '-'); ?></div>
                <div class="info-item"><strong>CIRCULAÇÃO :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['circulacao'] ?? '-'); ?></div>
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
                <div class="info-item"><strong>PLACA CARRETA :</strong>
                    <?php echo htmlspecialchars($dados_expedicao['placaCarreta'] ?? '-'); ?></div>
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
                <div class="info-item2"><strong>VOLUME CARREGADO:</strong>
                    <?php echo htmlspecialchars($dados_expedicao['vCarregado'] ?? '-'); ?> M³</div>
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
                    <span class="valor-vermelho"><span id="densidade">Kg/L</span></span></div>
                <div class="info-item2"><strong>FATOR CORREÇÃO: </strong>
                    <span class="valor-vermelho"><span id="fatorCorrecao">a 20 °C</span></span></div>
                <div class="info-item2"><strong>VOLUME CONVERTIDO:</strong>
                    <span class="valor-azul"><span id="volumeConvertido"></span> M³</span></div>
                <div class="info-item2"><strong>VOLUME CONVERTIDO (BALANÇA):</strong>
                    <span class="valor-azul"><span id="volumeConvertidoBalanca"></span> M³</span></div>
                <div class="info-item2"><strong>Δ VOLUME:</strong>
                    <span class="valor-azul"><span id="deltaVolume"></span> L</span></div>
                <div class="info-item2"><strong>VALOR DE EMBARQUE:</strong>
                    <span class="valor-azul"><span id="valorEmbarque"></span> Kg</span></div>

</div>



        <?php else: ?>
            <div class="message-box error-message">
                <p>Nenhum checklist encontrado para o ID fornecido ou ocorreu um erro.</p>
            </div>
        <?php endif; ?>


    </div>

<script src="../js/a.js"></script>
<script>

//////////////////////////////////////////////////////////////////////////
const Q66 = Number(<?php echo json_encode($dados_expedicao['densidade']); ?>);
const Q67 = Number(<?php echo json_encode($dados_expedicao['temperaturaAmostra']); ?>);
const Q68 = Number(<?php echo json_encode($dados_expedicao['temperaturaCarreta']); ?>);

// console.log(Q66);
// console.log(Q67);
// console.log(Q68);
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

const bb = Q66;
const R61 = tab1a(bb);
const S61 = tab2a(bb);
const T61 = tab1b(bb);
const U61 = tab2b(bb);
console.log("tab1a: " + R61);
console.log("tab2a: " + S61);
console.log("tab1b: " + T61);
console.log("tab2b: " + U61);

//////  
let V61 = (9/5) * 0.999042 * (R61 + 16 * T61 - (((8 * R61 + 64 * T61 ) * (S61 + 16 * U61))/( 1 + 8 * S61 + 64 * U61 )));
console.log("P1: " + V61);

let W61 = (9/5)*(S61+16*U61)/(1+8*S61+64*U61);
console.log("P2: " + W61);

let X61 = 81/25*0.999042*(T61-((8*R61+64*T61)*U61)/(1+8*S61+64*U61));
console.log("P3: " + X61);

let Y61 = 81/25*(U61/(1+8*S61+64*U61));
console.log("P4: " + Y61);

let Z61 = 1-0.000023 * (Q67-20) -0.00000002 * ((Q67-20) ** 2);
console.log("hyc: " + Z61);

let Q70 =(Q66-V61*(Q67-20)-X61*((Q67-20)**2))/(1+W61*(Q67-20)+Y61*((Q67-20)**2))*Z61;
console.log("Gravity Corrected 20oC: " + Q70);

let Q71 = 1+W61*(Q68-20)+Y61*((Q68-20)**2)+(V61*(Q68-20)+X61*((Q68-20)**2))/Q70;
console.log("Volume Conversion Factor: " + Q71);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function atualizarValores() {

    //dados fixos
    const densidade20 = Q70;
    const fatorCorrecao20 = Q71;

    //captura valores de densidade, temperaturas e volume da carreta.
    const volumeCarreta = <?php echo json_encode($dados_expedicao['volumeCarreta'] ?? '-'); ?>;
    const densidade = <?php echo json_encode($dados_expedicao['densidade'] ?? '-'); ?>;
    const temperaturaAmostra = <?php echo json_encode($dados_expedicao['temperaturaAmostra'] ?? '-'); ?>;
    const temperaturaCarreta = <?php echo json_encode($dados_expedicao['temperaturaCarreta'] ?? '-'); ?>;
    
    // 1. Pega o elemento de entrada (MASSA DA CARRETA)
    const massaCarretaInput = document.getElementById('massaCarreta');
    
    // 2. Pega o valor digitado
    const novoValor = massaCarretaInput.value;

    // 3. Pega os elementos que precisam ser atualizados
    const densidadeA = document.getElementById('densidade');
    const fatorCorrecaoA = document.getElementById('fatorCorrecao');
    densidadeA.textContent = densidade20;
    fatorCorrecaoA.textContent = fatorCorrecao20;

    const volumeConvertido = document.getElementById('volumeConvertido');
    const volumeConvertidoBalanca = document.getElementById('volumeConvertidoBalanca');
    const deltaVolume = document.getElementById('deltaVolume');
    const valorEmbarque = document.getElementById('valorEmbarque');

    // 4. Atualiza o conteúdo de cada elemento com o novo valor
    // Se o campo estiver vazio, podemos exibir um texto padrão ou um zero
    const valorFormatado = novoValor === '' ? '0' : novoValor;

    //cáuculos de conversão:
    calcVolumeConvertidoBalanca = novoValor/densidade20;

    calcVolumeConvertido = (volumeCarreta/1000*fatorCorrecao20);
    valorComPonto = calcVolumeConvertido.toFixed(3);
    valorFinal = valorComPonto.replace('.', ',');

    calcVolume = (calcVolumeConvertidoBalanca-(calcVolumeConvertido*1000));

    //valor embarque
    const resultado = Math.round(calcVolumeConvertido * 1000);
    const calcValorEmbarque = (resultado * 0.85) - novoValor;

    //////////////////////////////////////////////////////////
    //volume convertido
    volumeConvertido.textContent = valorFinal;
    //Volume convertido balança
    volumeConvertidoBalanca.textContent = Math.trunc(calcVolumeConvertidoBalanca);
    // delta volume
    deltaVolume.textContent = Math.trunc(calcVolume);

    valorEmbarque.textContent = Math.trunc(calcValorEmbarque);
    //////////////////////////////////////////////////////////

    //trocar de cor
    const v1 = volumeCarreta * 0.005;
    const v2 = volumeCarreta * -0.005;

    console.log(v1);
    console.log(v2);
    console.log(calcVolume);


    const divResultado = document.getElementById('resultadoConversao');

    if (calcVolume < v1 && calcVolume > v2){
        divResultado.style.backgroundColor = 'blue';
        console.log("azul");
    }else{
        console.log("vermelho");
        divResultado.style.backgroundColor = 'red';
    }

}

</script>
</body>

</html>
<?php $conn = null; ?>