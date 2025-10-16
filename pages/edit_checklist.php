<?php
require_once("../assets/connection.php");

session_start();

$id_expedicao = null;
$dados_expedicao = [];
$mensagem = '';

if (isset($_GET['id']) && !empty($_GET['id'])) {

    $id_expedicao = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $sql_select = "SELECT id, flow, ticket, produto, transportadora, nomeMotorista,
                   data, placaCavalo, cnhMotorista, horaEntrada, placaTanque1,
                   destino, responsavelBalanca, placaTanque2, volumeCarreta
                   FROM db_checklist.dbo.tb_marking 
                   WHERE id = :id";

    try {
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->bindParam(':id', $id_expedicao, PDO::PARAM_INT);
        $stmt_select->execute();
        $dados_expedicao = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if (!$dados_expedicao) {
            $mensagem = "TICKET" . htmlspecialchars($id_expedicao) . " não encontrado";
        }
    } catch (PDOException $e) {
        die("Erro ao carregar dados: " . $e->getMessage());
    }
} else {
    header("Location: expedicao.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
/* Estilo do Teclado Numérico Virtual */
.teclado-numerico {
    position: fixed; /* Fixa na tela */
    top: 50%;
    right: 20px;
    transform: translateY(-50%); /* Centraliza verticalmente */
    width: 250px; /* Largura do teclado */
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    z-index: 1000; /* Garante que fique acima de outros elementos */
    display: none; /* COMEÇA OCULTO */
}

.teclado-numerico.ativo {
    display: block; /* Torna-se visível quando 'ativo' */
}

.teclado-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #eee;
    background-color: #f7f7f7;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

#tecladoDisplay {
    font-size: 1.2em;
    font-weight: bold;
    color: #333;
}

.teclado-fechar {
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 5px 10px;
    cursor: pointer;
    font-size: 1em;
}

.teclado-botoes {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    padding: 10px;
}

.teclado-btn {
    padding: 15px 0;
    font-size: 1.2em;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f9f9f9;
    cursor: pointer;
    transition: background-color 0.1s;
    user-select: none; /* Impede a seleção de texto ao tocar */
}

.teclado-btn:active {
    background-color: #e0e0e0;
}

.teclado-btn.apagar, .teclado-btn.limpar {
    background-color: #f39c12;
    color: white;
}

.teclado-btn.enter {
    background-color: #2ecc71;
    color: white;
    font-weight: bold;
    grid-column: span 1; /* Ocupa uma coluna */
}

.teclado-btn.zero-duplo {
    grid-column: span 1;
}

/* Atualize estes estilos */
.teclado-numerico {
    position: fixed;
    /* top, right e transform serão manipulados pelo JS de arrastar */
    width: 250px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    display: none;
    cursor: move; /* Indica que o teclado é arrastável */
    
    /* Posição inicial (pode ser qualquer valor que você queira) */
    top: 150px; 
    right: 50px;
}

/* Ajustes no CSS para o novo botão fechar */
.teclado-btn.teclado-fechar {
    background: #e74c3c;
    color: white;
    font-size: 1.2em;
}

/* O resto do .teclado-botoes e .teclado-btn permanece igual */

/* Cor base dos botões: Fundo mais claro para contraste */
.teclado-btn {
    padding: 15px 0;
    font-size: 1.3em; /* Aumentado a fonte para melhor toque */
    border: 1px solid #005600; /* Borda forte para destacar */
    border-radius: 6px; /* Levemente mais arredondado */
    background-color: #e6ffe6; /* Verde muito claro */
    color: #000000; /* Cor preta para o texto/número */
    cursor: pointer;
    transition: background-color 0.1s;
    user-select: none;
    font-weight: bold; /* Números mais fortes */
}

/* Estado de clique/toque */
.teclado-btn:active {
    background-color: #ccffcc; /* Escurece um pouco ao ser tocado */
}

/* Botões de Ação Secundária (Limpar e Apagar) */
.teclado-btn.apagar, .teclado-btn.limpar {
    background-color: #ff9900; /* Laranja VIVO */
    color: white;
    border-color: #cc7a00;
}

/* Botão ENTER (Ação Principal/Positiva) */
.teclado-btn.enter {
    background-color: #27ae60; /* Um verde forte e sólido (tom da sua logo) */
    color: white;
    font-weight: bold;
    grid-column: span 1; 
    border-color: #1a7a44;
}

/* Botão Fechar (X) - Ação Negativa */
.teclado-btn.teclado-fechar {
    background: #c0392b; /* Vermelho forte para indicar fechar */
    color: white;
    font-size: 1.2em;
    border-color: #8c2a1f;
}

/* O resto do .teclado-botoes e .teclado-numerico permanece igual */




    </style>
</head>

<body>
    <?php
    //print_r($_SESSION);
    ?>
    <div class="container">
        <button id="bt_bk_exp">Voltar</button>
        <h3>Preenchimento do Checklist: <?php echo htmlspecialchars($dados_expedicao['ticket'] ?? ''); ?> </h3>

        <?php if ($mensagem): ?>
            <div class="message error">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <?php if ($dados_expedicao): ?>
            <table class="tableEdit">
                <thead>
                    <tr>
                        <th>produto</th>
                        <th>transportadora</th>
                        <th>nomeMotorista</th>
                        <th>data</th>
                        <th>horaEntrada</th>
                        <th>destino</th>
                        <th>responsavelBalanca</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['produto'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['transportadora'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['nomeMotorista'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['data'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['horaEntrada'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['destino'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['responsavelBalanca'] ?? '') . "</td>";
                    echo "</tr>";
                    ?>
                </tbody>
            </table>

            <div class="data-display">

                <form action="../assets/confirm_checklist.php?<?php echo htmlspecialchars($id_expedicao); ?>" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_expedicao) ?>">

                    <table>

                        <tbody>
                            <tr>
                                <td>Faróis, lanternas e setas em bom estado?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="farois" value="sim" required>Sim
                                        <input type="radio" name="farois" value="nao">Não
                                        <input type="radio" name="farois" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Vagões, válvulas e conexões isentos de vazamentos?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="vagoes" value="sim" required>Sim
                                        <input type="radio" name="vagoes" value="nao">Não
                                        <input type="radio" name="vagoes" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>No cavalo o painel de segurança e rótulo de risco meio ambiente estão conforme?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="cavalo" value="sim" required>Sim
                                        <input type="radio" name="cavalo" value="nao">Não
                                        <input type="radio" name="cavalo" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Os extintores estão com a validade da carga e teste hidrostático conformes?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="extintores" value="sim" required>Sim
                                        <input type="radio" name="extintores" value="nao">Não
                                        <input type="radio" name="extintores" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Verificado se há volume remanescente nos compartimentos da carreta a ser carregada?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="verificado" value="sim" required>Sim
                                        <input type="radio" name="verificado" value="nao">Não
                                        <input type="radio" name="verificado" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Se houver volume, drenou todas as bocas de saída?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="volume" value="sim" required>Sim
                                        <input type="radio" name="volume" value="nao">Não
                                        <input type="radio" name="volume" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Necessidade de lavar e/ou secar o tanque? (*Secar a umidade, se presente)</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="lavar" value="sim" required>Sim
                                        <input type="radio" name="lavar" value="nao">Não
                                        <input type="radio" name="lavar" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Setas de medidas de compartimento visíveis?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="setas" value="sim" required>Sim
                                        <input type="radio" name="setas" value="nao">Não
                                        <input type="radio" name="setas" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>A vedação da boca de carregamento está conforme?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="vedacao" value="sim" required>Sim
                                        <input type="radio" name="vedacao" value="nao">Não
                                        <input type="radio" name="vedacao" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Possui válvula de fundo de fecho rápido?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="valvula" value="sim" required>Sim
                                        <input type="radio" name="valvula" value="nao">Não
                                        <input type="radio" name="valvula" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Caminhão tanque em condições de realizar o transporte?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="transporte" value="sim" required>Sim
                                        <input type="radio" name="transporte" value="nao">Não
                                        <!-- <input type="radio" name="transporte" value="na">n/a</label> -->
                                </td>
                            </tr>
                            <tr>
                                <td>Certificado se os tubos de descargas (canos) irão ser carregados cheios ou vazio? </td>
                                <td class="radio-group">
                                    <label><input type="radio" name="tubos" value="sim" required>Sim
                                        <input type="radio" name="tubos" value="nao">Não
                                        <!-- <input type="radio" name="tubos" value="na">n/a</label> -->
                                </td>
                            </tr>
                            <tr>
                                <td>Carregamento aprovado? Caso não, informar à supervisão.</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="carregamento" value="sim" required>Sim
                                        <input type="radio" name="carregamento" value="nao">Não
                                        <!-- <input type="radio" name="carregamento" value="na">n/a</label> -->
                                </td>
                            </tr>

                        </tbody>


                    </table>

                    <div class="form-container">
                        <div class="form-group">
                            <label for="operador_expedicao">RESPONSÁVEL EXPEDIÇÃO: </label>
                            <input required type="text" id="responsavelExpedicao" name="responsavelExpedicao"
                                value="<?php echo htmlspecialchars($_SESSION['name'] ?? '') ?>">
                        </div>

                        <!-- II.III REGISTROS DO CARREGAMENTO -->

                        <div class="form-group">
                            <label for="laudo">LAUDO: </label>
                            <input class="input-numerico-virtual" required type="text" id="laudo" name="laudo" value="" autocomplete="on">
                        </div>

                        <div class="form-group">
                            <label for="baia">N° BAIA DE CARREGAMENTO/ BRAÇO: </label>
                            <input class="input-numerico-virtual" required type="text" id="baia" name="baia" value="" autocomplete="off" maxlength="1"
                                pattern="\d">
                        </div>

                        <div class="form-group">
                            <label for="temperaturaAmostra">TEMPERATURA DA AMOSTRA (°C): </label>
                            <input class="input-numerico-virtual" maxlength="4" required type="text" id="temperaturaAmostra" name="temperaturaAmostra"
                                value="" autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="densidade">DENSIDADE DA AMOSTRA: </label>
                            <input class="input-numerico-virtual" required type="text" id="densidade" name="densidade" value="" autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="temperaturaCarreta">TEMPERATURA DA CARRETA (°C): </label>
                            <input class="input-numerico-virtual" maxlength="4" required type="text" id="temperaturaCarreta" name="temperaturaCarreta"
                                value="" autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="lacresAmostra">LACRES DAS AMOSTRAS: </label>
                            <input class="input-numerico-virtual" required type="text" id="lacresAmostra" name="lacresAmostra" value=""
                             autocomplete="off" maxlength="27">
                        </div>

                        <div class="form-group">
                            <label for="lacreMotorista">LACRE DA AMOSTRA DO MOTORISTA: </label>
                            <input class="input-numerico-virtual" required type="text" id="lacreMotorista" name="lacreMotorista" value=""
                                autocomplete="off" maxlength="27">
                        </div>

                        <div class="form-group">
                            <label for="lacresCarreta">LACRES DA CARRETA: </label>
                            <input class="input-numerico-virtual" required type="text" id="lacresCarreta" name="lacresCarreta" value="" autocomplete="off" maxlength="1024">
                        </div>
                        <!-- II.III REGISTROS DO CARREGAMENTO -->

                        <div class="form-group">
                            <label for="obs">OBSERVAÇÃO: </label>
                            <input type="text" id="obs" name="obs" value="" autocomplete="off">
                        </div>
                    </div>

                    <button class="inserirDados" type="submit">Confirmar Checklist</button>


                </form>

            <?php else: ?>
                <p>Nenhum checklist disponível para preenchimento. </p>

            <?php endif; ?>
        </div>


<div id="tecladoNumerico" class="teclado-numerico">
    <div class="teclado-botoes">
        <button class="teclado-btn numero">1</button>
        <button class="teclado-btn numero">2</button>
        <button class="teclado-btn numero">3</button>
        <button id="tecladoFechar" class="teclado-btn teclado-fechar">X</button> <button class="teclado-btn numero">4</button>
        <button class="teclado-btn numero">5</button>
        <button class="teclado-btn numero">6</button>
        <button class="teclado-btn limpar">C</button>
        
        <button class="teclado-btn numero">7</button>
        <button class="teclado-btn numero">8</button>
        <button class="teclado-btn numero">9</button>
        <button class="teclado-btn apagar">←</button>
        
        <button class="teclado-btn numero-grande numero zero-duplo">00</button>
        <button class="teclado-btn numero zero">0</button>
        <button class="teclado-btn decimal">.</button>
        <button class="teclado-btn enter">OK</button>
    </div>
</div>





</body>

</html>
<?php $conn = null; ?>
<script src="../js/functions.js"></script>
<script>
    const inputDensidade = document.getElementById('densidade');

    // Função para aplicar a máscara 0.XXX
    function aplicarMascaraDensidade(event) {
        let valor = inputDensidade.value.replace(/[^0-9]/g, ''); // Remove tudo que não for número

        // Garante o prefixo '0.'
        if (!valor.startsWith('0')) {
            valor = '0' + valor;
        }

        // Adiciona o ponto após o primeiro zero
        if (valor.length > 1) {
            valor = valor.substring(0, 1) + '.' + valor.substring(1);
        }

        // Limita o valor a 5 caracteres (0.XXX)
        if (valor.length > 5) {
            valor = valor.substring(0, 5);
        }

        inputDensidade.value = valor;
    }

    // 1. Ao receber o foco (selecionar o campo): 
    // Garante que '0.' esteja no campo se ele estiver vazio.
    inputDensidade.addEventListener('focus', function () {
        if (inputDensidade.value === '') {
            inputDensidade.value = '0.';
        }
    });

    // 2. Enquanto estiver digitando no campo:
    // Aplica a máscara e a formatação
    inputDensidade.addEventListener('input', aplicarMascaraDensidade);

    // 3. Ao perder o foco (sair do campo):
    // Garante que o padrão (0.XXX) seja mantido (útil em alguns casos)
    inputDensidade.addEventListener('blur', function () {
        if (inputDensidade.value.length < 5) {
            // Se tiver menos de 5 caracteres (0.X ou 0.XX), preenche com zeros ou mantém o padrão '0.'
            // A validação 'pattern' do HTML se encarregará de exigir os 3 dígitos antes do envio
            if (inputDensidade.value === '0.') {
                // Mantém '0.' se o usuário não digitou nada após o ponto
            } else {
                aplicarMascaraDensidade(); // Reajusta
            }
        }
    });


    ///////////////////////////////////////////////////////////////////--


    ///////////////////////////////////////////////////////////////////--
    // CÓDIGO DA MÁSCARA DE DENSIDADE (0.XXX) - MANTIDO INTACTO
    // ... (Seu código original da densidade começa aqui e não deve ser alterado)
    ///////////////////////////////////////////////////////////////////--

    // Seleciona os elementos
    const inputTemperaturaCarreta = document.getElementById('temperaturaCarreta');
    const inputTemperaturaAmostra = document.getElementById('temperaturaAmostra');

    // Função única para aplicar a máscara XX.X
    function aplicarMascaraTemperatura(event) {
        const inputElement = this;

        // 1. Remove tudo que não for número do valor (o ponto será inserido pelo código)
        let valor = inputElement.value.replace(/[^0-9]/g, '');

        // 2. Limita o valor bruto (sem ponto) a no máximo 3 dígitos
        if (valor.length > 3) {
            valor = valor.substring(0, 3);
        }

        // 3. Aplica a formatação baseada no comprimento:
        if (valor.length <= 2) {
            // Se 1 ou 2 dígitos (Ex: 1 ou 15), mantém como inteiro
            inputElement.value = valor;
        } else if (valor.length === 3) {
            // Se 3 dígitos (Ex: 152), formata como XX.X
            valor = valor.substring(0, 2) + '.' + valor.substring(2);
            inputElement.value = valor;
        }
    }

    // Aplica a função de máscara para os dois campos
    inputTemperaturaCarreta.addEventListener('input', aplicarMascaraTemperatura);
    inputTemperaturaAmostra.addEventListener('input', aplicarMascaraTemperatura);

    // REMOVEMOS os event listeners de 'blur' e a manipulação de maxlength para evitar conflitos.

    ///////////////////////////////////////-

    ///////////////////////////////////////////////////////////////////--
    // Máscara para N° BAIA (somente um dígito numérico)
    const inputBaia = document.getElementById('baia');

    function permitirSomenteUmDigito(event) {
        let valor = inputBaia.value.replace(/[^0-9]/g, ''); // Remove tudo que não for número (letras, símbolos, etc.)
        
        // Limita o valor a 1 dígito (embora o maxlength="1" no HTML já ajude)
        if (valor.length > 1) {
            valor = valor.substring(0, 1);
        }
        
        inputBaia.value = valor;
    }

    inputBaia.addEventListener('input', permitirSomenteUmDigito);
    ////////////////////////////////////////////////////////////////////////-




    // ... seu código de densidade, temperatura e baia aqui ...

    ///////////////////////////////////////////////////////////////////--
    // Máscara para LAUDO (somente números, max 6)
    const inputLaudo = document.getElementById('laudo');

    function permitirSomenteNumeros(event) {
        // Remove tudo que não for número (letras, símbolos, espaços)
        let valor = inputLaudo.value.replace(/[^0-9]/g, '');
        
        // O maxlength="6" no HTML já impede a digitação do 7º, 
        // mas esta linha garante que o valor permaneça limpo
        if (valor.length > 6) {
            valor = valor.substring(0, 6);
        }
        
        inputLaudo.value = valor;
    }

    inputLaudo.addEventListener('input', permitirSomenteNumeros);
/////////////////////////////////////////////////////////////////////////-

// ... seu código de densidade, temperatura, baia e laudo aqui ...

    ///////////////////////////////////////////////////////////////////--
    // Máscara para LACRES (XXXXXX XXXXXX XXXXXX...)
    const inputLacresAmostra = document.getElementById('lacresAmostra');
    const inputLacreMotorista = document.getElementById('lacreMotorista');
    const inputLacresCarreta = document.getElementById('lacresCarreta');
    
    // Função única para formatar os lacres
    function aplicarMascaraLacres(event) {
        const inputElement = this; 
        
        // 1. Remove TUDO que não for número (limpa o valor)
        let valor = inputElement.value.replace(/[^0-9]/g, '');

        // 2. Aplica a formatação: insere um espaço a cada 6 dígitos
        // Expressão Regular: a cada 6 dígitos (\d{6}) que são seguidos por qualquer outro dígito, insere o espaço
        // O $1 refere-se ao grupo de 6 dígitos que foi encontrado
        valor = valor.replace(/(\d{6})(?=\d)/g, '$1 ');

        inputElement.value = valor;
    }

    // Aplica a mesma função de máscara para os três campos
    inputLacresAmostra.addEventListener('input', aplicarMascaraLacres);
    inputLacreMotorista.addEventListener('input', aplicarMascaraLacres);
    inputLacresCarreta.addEventListener('input', aplicarMascaraLacres);

// ... Todo o seu código de máscara (Densidade, Temperatura, Laudo, Baia, Lacres) deve vir aqui ANTES ...


// ... Todo o código das suas MÁSCARAS aqui (Densidade, Temperatura, etc.) ...

// ===================================================================
// LÓGICA DO TECLADO NUMÉRICO VIRTUAL (TOUCHSCREEN) ARRASTÁVEL
// ===================================================================
const teclado = document.getElementById('tecladoNumerico');
const botaoFechar = document.getElementById('tecladoFechar');
const botoes = teclado.querySelectorAll('.teclado-btn');

let campoAtivo = null;
let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0; // Posições para arrastar

// --- 1. DEFINIÇÃO DA ORDEM DOS CAMPOS (ADICIONE SEUS IDs AQUI) ---
// Liste os IDs dos campos *na ordem em que você quer que o foco se mova*.
const ORDEM_CAMPOS = [
    'laudo',
    'baia',
    'temperaturaAmostra',
    'densidade',
    'temperaturaCarreta',
    'lacresAmostra',
    'lacreMotorista',
    'lacresCarreta',
    // 'obs' - Não precisa se for preenchido com teclado normal
];

// Identifica todos os campos que usarão o teclado virtual
const camposParaTeclado = document.querySelectorAll('.input-numerico-virtual');
// Note: O querySelectorAll está sendo usado para o foco, mas a navegação usa ORDEM_CAMPOS

if (teclado && camposParaTeclado.length > 0) {
    // ... LÓGICA DE ARRASTAR (sem mudanças) ...
    teclado.onmousedown = dragMouseDown; 
    // (Resto das funções dragMouseDown, elementDrag, closeDragElement aqui...)

    function dragMouseDown(e) {
        e = e || window.event;
        if (e.target.closest('.teclado-btn')) return; 
        
        e.preventDefault();
        pos3 = e.clientX;
        pos4 = e.clientY;
        
        document.onmouseup = closeDragElement;
        document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        
        teclado.style.top = (teclado.offsetTop - pos2) + "px";
        teclado.style.left = (teclado.offsetLeft - pos1) + "px";
    }

    function closeDragElement() {
        document.onmouseup = null;
        document.onmousemove = null;
    }
    // ------------------------------------------

    // --- 2. NOVA LÓGICA DE NAVEGAÇÃO APÓS O BOTÃO OK ---
    
    // Função principal que move o foco e fecha o teclado
// Função principal que move o foco e fecha o teclado
function irParaProximoCampo(event) {
    // Fecha o teclado e remove a classe 'ativo'
    teclado.classList.remove('ativo');
    
    if (campoAtivo) {
        const campoID = campoAtivo.id;
        const currentIndex = ORDEM_CAMPOS.indexOf(campoID);
        
        // 1. Tira o foco do campo atual imediatamente
        campoAtivo.blur(); 
        campoAtivo = null; // Limpa o campo ativo

        if (currentIndex !== -1 && currentIndex < ORDEM_CAMPOS.length - 1) {
            // Se não for o último campo, prepara para focar no próximo
            const proximoID = ORDEM_CAMPOS[currentIndex + 1];
            const proximoCampo = document.getElementById(proximoID);
            
            if (proximoCampo) {
                // 2. Adiciona um pequeno atraso para focar no próximo campo
                // (20 milissegundos é geralmente suficiente para a maioria dos navegadores)
                setTimeout(() => {
                    proximoCampo.focus();
                }, 20); 
            }
        } 
        // Se for o último campo, ele simplesmente permanece sem foco (blur)
    }
}
    
    // Função para fechar o teclado (usada pelo botão 'X') sem avançar
    function fecharTecladoSemAvancar() {
        teclado.classList.remove('ativo');
        if (campoAtivo) {
            campoAtivo.blur();
        }
        campoAtivo = null;
    }
    // ------------------------------------------
    
    // --- LÓGICA DE EVENTOS ---
    camposParaTeclado.forEach(input => {
        input.addEventListener('focus', function() {
            campoAtivo = this;
            teclado.classList.add('ativo');
        });
        
        input.addEventListener('blur', function() {
            // Pequeno delay para verificar se o foco foi para o teclado
            setTimeout(() => {
                if (!teclado.contains(document.activeElement)) {
                    // fecharTecladoSemAvancar(); // Descomente se quiser que ele feche ao clicar em outro lugar
                }
            }, 10);
        });
    });

    // Mapeamento dos botões:
    // O botão ENTER (OK) chama a função de AVANÇAR
    teclado.querySelector('.enter').addEventListener('click', irParaProximoCampo);
    
    // O botão FECHAR (X) chama a função de FECHAR sem avançar
    botaoFechar.addEventListener('click', fecharTecladoSemAvancar);


    // Processa o toque nas teclas (sem mudanças nesta parte)
    botoes.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!campoAtivo) return; 
            e.preventDefault();
            // ... (Resto da lógica de inserção de números/ponto/apagar)
            const tecla = this.textContent.trim();
            let valorAtual = campoAtivo.value;
            let novoValor = valorAtual;

            if (this.classList.contains('numero')) {
                novoValor = valorAtual + tecla;
            } else if (this.classList.contains('decimal')) {
                // Permite o ponto APENAS em campos de densidade/temperatura
                if (campoAtivo.id !== 'densidade' && 
                    campoAtivo.id !== 'temperaturaAmostra' && 
                    campoAtivo.id !== 'temperaturaCarreta') {
                    return; // Ignora o ponto
                }
                if (valorAtual.includes('.')) return;
                novoValor = valorAtual + '.';
            } else if (this.classList.contains('apagar')) {
                novoValor = valorAtual.substring(0, valorAtual.length - 1);
            } else if (this.classList.contains('limpar')) {
                novoValor = '';
            }

            campoAtivo.value = novoValor;
            campoAtivo.dispatchEvent(new Event('input', { bubbles: true }));
        });
    });
}

</script>