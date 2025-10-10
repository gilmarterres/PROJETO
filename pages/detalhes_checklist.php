<?php

// Requer a conexão com o banco de dados
// CERTIFIQUE-SE DE QUE ESTE CAMINHO ESTÁ CORRETO
require_once("../assets/connection.php");

// Inicia a sessão
session_start();

$id_expedicao = null;
$dados_expedicao = [];
$mensagem = '';
$mensagem_tipo = '';

// --- CONSTANTES DE CONVERSÃO ---
const DENSIDADE_20 = 0.879; // Kg/L (Fixo)
const FATOR_CORRECAO = 0.992440; // a 20 °C (Fixo)

// --- Definição de Nomes Amigáveis e Grupos de Campos ---

// Campos a serem excluídos da exibição genérica (ex: campos de controle interno)
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

    // Itens de Checklist (perguntas)
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

    // Campos de Informações Adicionais/Laudo
    'responsavelExpedicao' => 'Responsável Expedição',
    'laudo' => 'Laudo',
    'baia' => 'N° Baia de Carregamento/Braço',
    'temperaturaAmostra' => 'Temperatura da Amostra (°C)',
    'densidade' => 'Densidade da Amostra',
    'temperaturaCarreta' => 'Temperatura da Carreta (°C)',
    'lacresAmostra' => 'Lacres das Amostras',
    'lacreMotorista' => 'Lacre da Amostra do Motorista',
    'lacresCarreta' => 'Lacres da Carreta',

    // NOVOS CAMPOS PARA CONVERSÃO
    'massaCarreta' => 'Massa da Carreta:', // Campo manual (adicionado)
    'densidade20Display' => 'DENSIDADE 20°:', // Fixo
    'fatorCorrecaoDisplay' => 'FATOR CORREÇÃO:', // Fixo
    'volumeConvertido' => 'VOLUME CONVERTIDO:', // Calculado (esquerda)
    'volumeConvertidoBalanca' => 'VOLUME CONVERTIDO (BALANÇA)', // Calculado (direita)
    'DeltaVolume' => 'Δ VOLUME', // Calculado (direita)
    'valorEmbarque' => 'VALOR DE EMBARQUE', // Calculado (direita)
];

// Organização dos campos em grupos para exibição no HTML
$gruposCampos = [
    'Dados Gerais do Carregamento' => [
        'data',
        'horaEntrada',
        'produto',
        'volumeCarreta',
        'destino',
        'obs',
        'responsavelBalanca',
        'responsavelExpedicao'
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
    'Informações de Laudo e Carregamento' => [
        'laudo',
        'baia',
        'temperaturaAmostra',
        'densidade',
        'temperaturaCarreta',
        'lacresAmostra',
        'lacreMotorista',
        'lacresCarreta',
    ],
    // NOVO GRUPO
    'RESULTADO DA CONVERSÃO DOS DADOS' => [
        'massaCarreta', // Adicionado como input manual para permitir o cálculo
        'densidade20Display',
        'fatorCorrecaoDisplay',
        'volumeConvertido',
        'volumeConvertidoBalanca',
        'DeltaVolume',
        'valorEmbarque',
    ]
];

// --- Função de Formatação ---
function formatChecklistValue(string $key, $value, array $nomesAmigaveis): array
{
    $displayValue = '';
    $statusClass = '';
    $label = $nomesAmigaveis[$key] ?? ucwords(str_replace(['_', 'responsavel'], [' ', 'Resp. '], $key));

    // Formatação de Data/Hora
    if ($key === 'data' && !empty($value)) {
        try {
            $dateTime = new DateTime($value);
            $displayValue = $dateTime->format('d/m/Y');
        } catch (Exception $e) {
            $displayValue = htmlspecialchars($value);
        }
    } elseif ($key === 'horaEntrada' && !empty($value)) {
        try {
            $dateTime = new DateTime($value);
            $displayValue = $dateTime->format('H:i');
        } catch (Exception $e) {
            $displayValue = htmlspecialchars($value);
        }
    }
    // Formatação dos Itens de Checklist Sim/Não/N/A
    elseif (in_array($key, array_keys(array_intersect_key($nomesAmigaveis, array_flip(['farois', 'vagoes', 'cavalo', 'extintores', 'verificado', 'lavar', 'vedacao', 'valvula', 'transporte', 'tubos', 'carregamento']))))) {
        $normalizedValue = strtolower((string) $value);
        if ($normalizedValue === 'sim') {
            $displayValue = 'Sim';
            $statusClass = 'status-sim';
        } elseif ($normalizedValue === 'nao') {
            $displayValue = 'Não';
            $statusClass = 'status-nao';
        } elseif ($normalizedValue === 'na') {
            $displayValue = 'N/A';
            $statusClass = 'status-na';
        } else {
            $displayValue = htmlspecialchars((string) $value);
            $statusClass = 'status-desconhecido';
        }
    }
    // Formatação Padrão (demais campos)
    else {
        $displayValue = htmlspecialchars((string) $value);
    }

    return [
        'label' => $label,
        'displayValue' => ($displayValue !== '' ? $displayValue : '-'),
        'statusClass' => $statusClass,
        'isChecklistItem' => in_array($key, array_keys(array_intersect_key($nomesAmigaveis, array_flip(['farois', 'vagoes', 'cavalo', 'extintores', 'verificado', 'lavar', 'vedacao', 'valvula', 'transporte', 'tubos', 'carregamento']))))
    ];
}


// --- Lógica de Busca no Banco de Dados ---
if (isset($conn) && isset($_GET['id']) && !empty($_GET['id'])) {
    $id_expedicao = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $sql_select = "SELECT * FROM db_checklist.dbo.tb_marking WHERE id = :id";
    try {
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->bindParam(':id', $id_expedicao, PDO::PARAM_INT);
        $stmt_select->execute();
        $dados_expedicao = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if (!$dados_expedicao) {
            $mensagem = "Checklist com ID " . htmlspecialchars($id_expedicao) . " não encontrado.";
            $mensagem_tipo = 'error';
        }
    } catch (PDOException $e) {
        $mensagem = "Erro ao carregar dados: " . $e->getMessage();
        $mensagem_tipo = 'error';
    }
} else {
    $mensagem = "ID do checklist não fornecido ou erro de conexão.";
    $mensagem_tipo = 'error';
}

$dados_filtrados = array_diff_key($dados_expedicao, array_flip($camposExcluidos));


// --- CÁLCULOS DA CONVERSÃO ---

$volumeCarreta = (float) ($dados_filtrados['volumeCarreta'] ?? 0);

// Massa da Carreta: Deve ser obtida de um input. Simulamos aqui lendo de um campo de POST
// Para fins de visualização, se não houver POST, usamos um valor padrão (50000)
$massaCarretaInput = (float) ($_POST['massaCarreta'] ?? 50000);

$resultadosConversao = [];

if ($dados_filtrados) {

    // DADOS FIXOS E MASSA MANUAL
    $resultadosConversao['massaCarreta'] = $massaCarretaInput; // Valor bruto para o input
    $resultadosConversao['densidade20Display'] = number_format(DENSIDADE_20, 3, ',', '') . ' Kg/L';
    $resultadosConversao['fatorCorrecaoDisplay'] = number_format(FATOR_CORRECAO, 6, ',', '') . ' a 20 °C';

    // 1. VOLUME CONVERTIDO (m³)
    // Fórmula: (volume da carreta) / 1000 * fator correção
    $volumeConvertido_m3 = ($volumeCarreta / 1000) * FATOR_CORRECAO;
    $resultadosConversao['volumeConvertido'] = number_format($volumeConvertido_m3, 3, ',', '') . ' M³';

    // 2. VOLUME CONVERTIDO (BALANÇA) (L)
    // Fórmula: MASSA DA CARRETA / DENSIDADE 20°
    $valorConvertidoBalanca_L = ($massaCarretaInput > 0 && DENSIDADE_20 > 0)
        ? ($massaCarretaInput / DENSIDADE_20)
        : 0;

    // Conversão de L para M³ para exibição (como na imagem)
    $valorConvertidoBalanca_m3 = $valorConvertidoBalanca_L / 1000;

    $resultadosConversao['volumeConvertidoBalanca'] = number_format($valorConvertidoBalanca_m3, 3, ',', '') . ' M³';

    // 3. Δ VOLUME (L)
    // Sua fórmula: VALOR CONVERTIDO (BALANÇA) [L] - (VOLUME CONVERTIDO [m³] * 10000)
    $DeltaVolume = $valorConvertidoBalanca_L - ($volumeConvertido_m3 * 10000);
    $resultadosConversao['DeltaVolume'] = number_format($DeltaVolume, 0, ',', '') . ' L';

    // 4. VALOR DE EMBARQUE (Kg)
    // Sua fórmula: (((VOLUME CONVERTIDO [m³] * 10000) * 0,85) - VOLUME DA CARRETA [L])
    $valorEmbarque = (($volumeConvertido_m3 * 10000) * 0.85) - $volumeCarreta;
    $resultadosConversao['valorEmbarque'] = number_format($valorEmbarque, 2, ',', '.') . ' Kg';
}

// Junta os resultados calculados aos dados filtrados
$dados_filtrados = array_merge($dados_filtrados, $resultadosConversao);

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

        /* Botão Voltar */
        .back-button {
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
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #45a049;
        }

        /* Seções de Informação Padrão */
        .info-section {
            background-color: #f0f8f0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #d2e6d2;
        }

        .info-section h2 {
            color: #3e8e41;
            font-size: 1.2em;
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #d2e6d2;
        }

        /* Título do Bloco de Conversão */
        .conversion-header {
            background-color: #0000ff;
            /* Azul da imagem */
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 1.2em;
            font-style: italic;
            font-weight: bold;
            margin: -15px -15px 15px -15px;
            /* Estende para as bordas do section */
            border-radius: 5px 5px 0 0;
        }

        .conversion-section {
            background-color: #ffffcc;
            /* Amarelo claro da imagem */
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }


        .info-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px 20px;
        }

        .info-item {
            flex: 1 1 calc(33.33% - 20px);
            font-size: 0.9em;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }

        .info-item strong {
            font-weight: bold;
            margin-right: 5px;
            color: #555;
        }

        /* Estilos do Checklist */
        .checklist-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
            flex: 1 1 100%;
        }

        .checklist-item .question {
            flex-grow: 1;
            margin-right: 15px;
            font-size: 0.95em;
        }

        .checklist-item .options {
            flex-shrink: 0;
            font-size: 0.95em;
        }

        /* Statuses de Resposta */
        .status-sim {
            color: #27ae60;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 3px;
            background-color: #e6f6e6;
        }

        .status-nao {
            color: #c0392b;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 3px;
            background-color: #fce6e6;
        }

        .status-na {
            color: #3498db;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 3px;
            background-color: #e6f3fc;
        }

        /* --- CSS para a Seção de Conversão --- */

        .conversion-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            /* Duas colunas principais */
            gap: 15px 30px;
            padding-top: 10px;
            font-size: 1.1em;
        }

        .conversion-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 0;
        }

        /* Labels e Valores alinhados à direita */
        .conversion-item:nth-child(even) {
            justify-content: flex-end;
        }

        .conversion-item .label {
            font-weight: bold;
            color: #333;
            flex-shrink: 0;
            margin-right: 10px;
        }

        .conversion-item .value-box {
            background-color: #fff;
            border: 1px solid #999;
            padding: 5px 8px;
            border-radius: 3px;
            font-weight: bold;
            color: #0000ff;
            /* Cor azul para valores (como na imagem) */
            text-align: right;
            width: 90px;
            flex-shrink: 0;
            margin-left: auto;
            /* Empurra o valor para a direita */
            margin-right: 5px;
        }

        /* Cor de destaque para Fixo/Manual */
        .conversion-item .value-box.fixed,
        .conversion-item input.manual-input {
            color: #c0392b;
            /* Vermelho/Laranja para DADOS FIXOS e MANUAL */
        }

        .conversion-item .value-box.fixed {
            background-color: #f0f0f0;
            /* Fundo diferente para fixo/manual */
        }

        .conversion-item .unit {
            font-size: 0.9em;
            flex-shrink: 0;
            color: #555;
            min-width: 30px;
        }

        .conversion-item input.manual-input {
            background-color: #fff;
            border: 1px solid #999;
            padding: 5px 8px;
            border-radius: 3px;
            width: 90px;
            text-align: right;
            font-weight: bold;
            flex-shrink: 0;
            margin-left: auto;
            margin-right: 5px;
        }

        /* Mensagens */
        .message-box {
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
            font-size: 1.1em;
        }

        .message-box.error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsividade */
        @media (max-width: 768px) {

            .info-item,
            .checklist-item {
                flex: 1 1 100%;
            }

            .conversion-grid {
                grid-template-columns: 1fr;
            }

            .conversion-item {
                justify-content: flex-start;
            }

            .conversion-item .value-box,
            .conversion-item input.manual-input {
                margin-left: auto;
            }
        }
    </style>
</head>

<body>
    <a href="#" onclick="history.back();" class="back-button">Voltar</a>
    <div class="container">
        <div class="header-green">
            <h1>Detalhes do Checklist #<?php echo htmlspecialchars($id_expedicao ?? 'N/A'); ?></h1>
        </div>

        <?php if ($mensagem): ?>
            <div class="message-box <?php echo $mensagem_tipo === 'error' ? 'error-message' : 'warning-message'; ?>">
                <p><?php echo $mensagem; ?></p>
            </div>
        <?php elseif ($dados_filtrados): ?>

            <form method="POST" action="?id=<?php echo htmlspecialchars($id_expedicao); ?>">

                <?php foreach ($gruposCampos as $titulo_grupo => $campos): ?>

                    <?php if ($titulo_grupo === 'RESULTADO DA CONVERSÃO DOS DADOS'): ?>
                        <div class="conversion-section">
                            <div class="conversion-header"><?php echo htmlspecialchars($titulo_grupo); ?></div>
                            <div class="conversion-grid">

                                <?php
                                // Lista de campos que estarão no layout de conversão, na ordem da imagem (esquerda/direita)
                                $campos_ordenados = [
                                    'densidade20Display',
                                    'volumeConvertidoBalanca',
                                    'fatorCorrecaoDisplay',
                                    'DeltaVolume',
                                    'volumeConvertido',
                                    'valorEmbarque',
                                    'massaCarreta' // Adicionado no final para ter um espaço próprio abaixo
                                ];

                                // Usamos este loop para criar o layout de 2 colunas da imagem
                                $i = 0;
                                while ($i < count($campos_ordenados)) {
                                    $key_left = $campos_ordenados[$i++];
                                    $key_right = $campos_ordenados[$i++] ?? null; // Pega o próximo item ou null

                                    // Se for o campo Massa da Carreta (que precisa de um input)
                                    if ($key_left === 'massaCarreta') {
                                        // Adicionamos um campo extra para a Massa da Carreta, fora da grade principal se necessário.
                                        // Ou, para simplificar a estrutura do grid, podemos exibi-lo onde ele foi colocado.
                                        // Vamos exibi-lo como uma linha separada, forçando o layout abaixo do grid.
                                        continue;
                                    }

                                    // Exibição dos itens em 2 colunas
                                    $data_left = formatChecklistValue($key_left, $dados_filtrados[$key_left] ?? '', $nomesAmigaveis);
                                    $parts_left = explode(' ', $data_left['displayValue']);
                                    $value_left = htmlspecialchars($parts_left[0]);
                                    $unit_left = htmlspecialchars($parts_left[1] ?? '');

                                    $data_right = $key_right ? formatChecklistValue($key_right, $dados_filtrados[$key_right] ?? '', $nomesAmigaveis) : null;
                                    $parts_right = $data_right ? explode(' ', $data_right['displayValue']) : [null, null];
                                    $value_right = htmlspecialchars($parts_right[0] ?? '');
                                    $unit_right = htmlspecialchars($parts_right[1] ?? '');

                                    // Bloco da ESQUERDA
                                    echo '<div class="conversion-item conversion-left">';
                                    echo '<span class="label">' . htmlspecialchars($data_left['label']) . '</span>';

                                    // DENSIDADE e FATOR CORREÇÃO são fixos (vermelho na imagem)
                                    $isFixed = in_array($key_left, ['densidade20Display', 'fatorCorrecaoDisplay']);

                                    echo '<div class="value-box' . ($isFixed ? ' fixed' : '') . '">' . $value_left . '</div>';
                                    echo '<span class="unit">' . $unit_left . '</span>';
                                    echo '</div>';

                                    // Bloco da DIREITA
                                    if ($key_right) {
                                        echo '<div class="conversion-item conversion-right">';
                                        echo '<span class="label">' . htmlspecialchars($data_right['label']) . '</span>';
                                        echo '<div class="value-box">' . $value_right . '</div>';
                                        echo '<span class="unit">' . $unit_right . '</span>';
                                        echo '</div>';
                                    }
                                }
                                ?>
                            </div>

                            <div style="clear: both; margin-top: 20px;">
                                <div class="conversion-item" style="justify-content: flex-start; border-top: 1px dashed #aaa; padding-top: 15px;">
                                    <span class="label"><?php echo htmlspecialchars($nomesAmigaveis['massaCarreta']); ?></span>
                                    <input type="text" name="massaCarreta" class="manual-input"
                                        value="<?php echo number_format($dados_filtrados['massaCarreta'] ?? 0, 2, ',', ''); ?>"
                                        placeholder="0,00"
                                        style="margin-left: 10px;">
                                    <span class="unit">Kg</span>
                                    <button type="submit" class="back-button" style="background-color: #3498db; margin-left: 20px;">Recalcular</button>
                                </div>
                                <small style="display: block; text-align: right; color: #555; margin-top: 5px;">* Preencha a Massa da Carreta e clique em Recalcular para atualizar os valores.</small>

                            </div>

                        </div>
                    <?php else: ?>
                        <div class="info-section">
                            <h2><?php echo htmlspecialchars($titulo_grupo); ?></h2>
                            <div class="info-grid">
                                <?php foreach ($campos as $key): ?>
                                    <?php
                                    $valor = $dados_filtrados[$key] ?? '';
                                    $formatedData = formatChecklistValue($key, $valor, $nomesAmigaveis);
                                    ?>
                                    <?php if ($formatedData['isChecklistItem']): ?>
                                        <div class="checklist-item">
                                            <div class="question">
                                                <?php echo htmlspecialchars($formatedData['label']); ?>
                                            </div>
                                            <div class="options">
                                                <span class="<?php echo $formatedData['statusClass']; ?>">
                                                    <?php echo $formatedData['displayValue']; ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="info-item">
                                            <strong><?php echo htmlspecialchars(strtoupper($formatedData['label'])); ?>:</strong>
                                            <span class="<?php echo $formatedData['statusClass']; ?>">
                                                <?php echo $formatedData['displayValue']; ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endforeach; ?>
            </form>

        <?php else: ?>
            <div class="message-box error-message">
                <p>Nenhum checklist encontrado para o ID fornecido ou ocorreu um erro.</p>
            </div>
        <?php endif; ?>

    </div>
</body>

</html>
<?php
// Fecha a conexão com o banco de dados
if (isset($conn)) {
    $conn = null;
}
?>