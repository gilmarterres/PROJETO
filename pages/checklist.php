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
                    <span class="valor-vermelho"><span id="densidade">0,879 Kg/L</span></span></div>
                <div class="info-item2"><strong>FATOR CORREÇÃO: </strong>
                    <span class="valor-vermelho"><span id="fatorCorrecao">0,992440 a 20 °C</span></span></div>
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


<script>

const valor1 = Number(<?php echo json_encode($dados_expedicao['densidade']); ?>);
const valor2 = Number(<?php echo json_encode($dados_expedicao['temperaturaAmostra']); ?>);
const valor3 = Number(<?php echo json_encode($dados_expedicao['temperaturaCarreta']); ?>);

console.log(valor1);
console.log(valor2);
console.log(valor3);
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// --- DADOS DA TABELA DE COEFICIENTES (P, R, S, T, U) ---
const COEFICIENTES_TABELA = [
    { min: 0.0000, R: -0.0024620000, S: 0.0032150000, T: -0.0000101400, U: 0.0000173800 },
    { min: 0.4980, R: -0.0023910000, S: 0.0030740000, T: -0.0000084100, U: 0.0000139800 },
    { min: 0.5180, R: -0.0022940000, S: 0.0028870000, T: -0.0000083900, U: 0.0000138700 },
    { min: 0.5390, R: -0.0021460000, S: 0.0026150000, T: -0.0000054600, U: 0.0000085500 },
    { min: 0.5590, R: -0.0019200000, S: 0.0022140000, T: -0.0000055100, U: 0.0000085900 },
    { min: 0.5790, R: -0.0023580000, S: 0.0029620000, T: -0.0000122500, U: 0.0002015000 },
    { min: 0.6000, R: -0.0013610000, S: 0.0013000000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.6150, R: -0.0012370000, S: 0.0011000000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.6350, R: -0.0010770000, S: 0.0008500000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.6550, R: -0.0010110000, S: 0.0007500000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.6750, R: -0.0009770000, S: 0.0007000000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.6950, R: -0.0010050000, S: 0.0007400000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.7460, R: -0.0012380000, S: 0.0010500000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.7660, R: -0.0010840000, S: 0.0008500000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.7860, R: -0.0009650000, S: 0.0007000000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.8060, R: -0.0008435000, S: 0.0005500000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.8260, R: -0.0007190000, S: 0.0004000000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.8460, R: -0.0006170000, S: 0.0002800000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.8710, R: -0.0005120000, S: 0.0001600000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.8960, R: -0.0003948000, S: 0.0003000000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.9960, R: -0.0005426000, S: 0.0001778000, T: 0.0000023100, U: -0.0000022000 }
].sort((a, b) => a.min - b.min);

/**
 * Função auxiliar que calcula as constantes V61, X61, W61, Y61 (dependem da Gravidade Observada).
 *
 * @param {number} gravidadeObservada - O valor da Gravidade Observada (O66/Q66).
 * @returns {object} Um objeto contendo as constantes V61, X61, W61, Y61, e Z61.
 */
function getCorrecaoConstantes(gravidadeObservada, tempAmostra) {
    // 1. Simulação do PROCV (VLOOKUP) com 'busca aproximada'
    let linhaCoeficiente = COEFICIENTES_TABELA[0];
    for (const row of COEFICIENTES_TABELA) {
        if (gravidadeObservada >= row.min) {
            linhaCoeficiente = row;
        } else {
            break;
        }
    }

    const R61 = linhaCoeficiente.R; // Coeficiente TAB1A
    const S61 = linhaCoeficiente.S; // Coeficiente TAB2A
    const T61 = linhaCoeficiente.T; // Coeficiente TAB1B
    const U61 = linhaCoeficiente.U; // Coeficiente TAB2B
    
    // Variável Delta T (usada apenas para Z61)
    const deltaT_amostra = tempAmostra - 20;

    // Denominador Comum para V61, X61, W61, Y61
    const denominadorComum = 1 + 8 * S61 + 64 * U61;

    // V61
    const numeradorV61 = R61 + 16 * T61 - (8 * R61 + 64 * T61) * U61;
    const V61 = (9 / 5) * 0.999042 * numeradorV61 / denominadorComum;

    // X61
    const numeradorX61 = T61 - (8 * R61 + 64 * T61) * U61;
    const X61 = (81 / 25) * 0.999042 * numeradorX61 / denominadorComum;

    // W61
    const W61 = (9 / 5) * (S61 + 16 * U61) / denominadorComum;

    // Y61
    const Y61 = (81 / 25) * U61 / denominadorComum;

    // Z61 (Calculado com base na temperatura da amostra)
    const Z61 = 1 - 0.000023 * deltaT_amostra - 0.00000002 * Math.pow(deltaT_amostra, 2);

    return { V61, X61, W61, Y61, Z61 };
}

// -----------------------------------------------------------
// 1. FUNÇÃO ANTERIOR (Gravity Corrected 20C - Q70)
// -----------------------------------------------------------

/**
 * Calcula a Gravidade Corrigida para 20°C (Gravity Corrected 20C - Q70).
 *
 * @param {number} gravidadeObservada - O valor da Gravidade Observada (O66/Q66).
 * @param {number} tempAmostra - A Temperatura da Amostra (O67).
 * @returns {number} O valor da Gravidade Corrigida a 20C (Q70).
 */
function calcularGravidadeCorrigida(gravidadeObservada, tempAmostra) {
    const { V61, X61, W61, Y61, Z61 } = getCorrecaoConstantes(gravidadeObservada, tempAmostra);
    const deltaT = tempAmostra - 20;

    // Q70 = (O66 - V61*deltaT - X61*deltaT^2) / (1 + W61*deltaT + Y61*deltaT^2) * Z61
    const numeradorQ70 = gravidadeObservada - V61 * deltaT - X61 * Math.pow(deltaT, 2);
    const denominadorQ70 = 1 + W61 * deltaT + Y61 * Math.pow(deltaT, 2);
    
    return (numeradorQ70 / denominadorQ70) * Z61;
}

// -----------------------------------------------------------
// 2. NOVA FUNÇÃO (Volume Conversion Factor - Q71)
// -----------------------------------------------------------

/**
 * Calcula o Fator de Conversão de Volume (Volume Conversion Factor - Q71).
 *
 * @param {number} gravidadeObservada - O valor da Gravidade Observada (O66/Q66).
 * @param {number} tempAmostra - A Temperatura da Amostra (O67).
 * @param {number} tempAmbiente - A Temperatura Ambiente (Q68).
 * @returns {number} O Fator de Conversão de Volume (Q71).
 */
function calcularFatorConversaoVolume(gravidadeObservada, tempAmostra, tempAmbiente) {
    // 1. Obter o resultado Q70
    const Q70 = calcularGravidadeCorrigida(gravidadeObservada, tempAmostra);
    
    // 2. Obter as constantes que dependem apenas da Gravidade (V61, X61, W61, Y61)
    const { V61, X61, W61, Y61 } = getCorrecaoConstantes(gravidadeObservada, tempAmostra);
    
    const deltaT_ambiente = tempAmbiente - 20;

    // Numerador Q71 (parte do termo 2): (V61*deltaT_ambiente + X61*deltaT_ambiente^2)
    const numerador_termo2 = V61 * deltaT_ambiente + X61 * Math.pow(deltaT_ambiente, 2);

    // Termo 1: (1 + W61*deltaT_ambiente + Y61*deltaT_ambiente^2)
    const termo1 = 1 + W61 * deltaT_ambiente + Y61 * Math.pow(deltaT_ambiente, 2);
    
    // Termo 2: (Numerador_termo2 / Q70)
    const termo2 = numerador_termo2 / Q70;

    // Q71 = Termo 1 + Termo 2
    return termo1 + termo2;
}

// --- EXEMPLO DE USO COM OS SEUS DADOS DE ENTRADA ---      // Q66
const GRAVIDADE = Number(valor1);
const TEMPERATURA_AMOSTRA = Number(valor2); // Q67
const TEMPERATURA_AMBIENTE = Number(valor3);  // Q68

// A. Calculando a Gravidade Corrigida (Q70)
const Q70_RESULTADO = calcularGravidadeCorrigida(GRAVIDADE, TEMPERATURA_AMOSTRA);

// B. Calculando o Fator de Conversão de Volume (Q71)
const Q71_RESULTADO = calcularFatorConversaoVolume(GRAVIDADE, TEMPERATURA_AMOSTRA, TEMPERATURA_AMBIENTE);

console.log(`Gravidade Corrigida 20C (Q70): ${Q70_RESULTADO}`); 
console.log(`Fator de Conversão de Volume (Q71): ${Q71_RESULTADO}`);

const vDensidade = document.getElementById('densidade');
const vFatorCorrecao = document.getElementById('fatorCorrecao');
vDensidade.textContent = Q70_RESULTADO;
vFatorCorrecao.textContent = Q71_RESULTADO;


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function atualizarValores() {

    //dados fixos
    const densidade20 = Q70_RESULTADO;
    const fatorCorrecao20 = Q70_RESULTADO;

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