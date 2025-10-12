<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo checklist</title>
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>
    <?php
    date_default_timezone_set('America/Sao_Paulo');
    $today = date('Y-m-d');
    $time = date('H:i');

    session_start();
    //print_r($_SESSION);
    //print_r($_GET);
    ?>
    <br>
    <button id="bt_back">Voltar</button>
    <div class="container">

        <h1>Novo Checklist</h1>

        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'sucess') {
                echo "<script>";
                echo "alert('Dados inseridos com sucesso!');";
                echo "</script>";
            } elseif ($_GET['status'] == 'error') {
                $error_message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Ocorreu um erro ao inserir os dados.';
                echo '<div class="message error">' . $error_message . '</div>';
            }
        }
        ?>

        <form action="../assets/insert_checklist.php" method="post">
            <fieldset>
                <legend class="header-legend">Informações Gerais</legend>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ticket">N° Ticket/Circulação:</label>
                        <input required type="text" id="ticket" name="ticket" autocomplete="off" maxlength="6"
                            pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                    <div class="form-group">
                        <label for="produto">Produto:</label>
                        <select required id="produto" name="produto">
                            <option value="" disabled selected>Selecione um produto...</option>
                            <option value="Biodiesel">Biodiesel</option>
                            <option value="Glicerina">Glicerina</option>
                            <option value="Borra">Borra</option>
                            <option value="Metanol">Oleína</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transportadora">Transportadora:</label>
                        <input required type="text" id="transportadora" name="transportadora" autocomplete="off"
                            oninput="formatTitleCase(this)">
                    </div>
                    <div class="form-group">
                        <label for="cnh_motorista">CNH motorista:</label>
                        <input required type="text" id="cnh_motorista" name="cnhMotorista" maxlength="11"
                            autocomplete="off" oninput="validarCNH(this.value)"
                            onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                        <span id="cnh_feedback" style="font-size: 0.8em; margin-top: 5px; display: block;"></span>
                    </div>
                    <div class="form-group">
                        <label for="nome_motorista">Nome do motorista:</label>
                        <input type="text" id="nome_motorista" name="nomeMotorista" oninput="formatTitleCase(this)"
                            autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="placaCavalo">Placa do cavalo:</label>
                        <input required type="text" id="placaCavalo" name="placaCavalo" autocomplete="off" maxlength="7"
                            minlength="7" oninput="validarPlaca(this.value, this.id)"
                            style="text-transform: uppercase;">
                    </div>

                    <div class="form-group">
                        <label for="placaTanque1">Placa Tanque 1:</label>
                        <input required type="text" id="placaTanque1" name="placaTanque1" autocomplete="off"
                            maxlength="7" minlength="7" oninput="validarPlaca(this.value, this.id)"
                            style="text-transform: uppercase;">
                    </div>
                    <div class="form-group">
                        <label for="placaTanque2">Placa Tanque 2:</label>
                        <input required type="text" id="placaTanque2" name="placaTanque2" autocomplete="off"
                            maxlength="7" minlength="7" oninput="validarPlaca(this.value, this.id)"
                            style="text-transform: uppercase;">
                    </div>
                    <div class="form-group">
                        <label for="destino">Destino:</label>
                        <input required type="text" id="destino" name="destino" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="volumeCarreta">Volume da carreta:</label>
                        <input required type="text" id="volumeCarreta" name="volumeCarreta" autocomplete="off"
                            maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                    <div class="form-group">
                        <label for="data">Data:</label>
                        <input required type="date" id="data" name="data" value="<?php echo $today ?>"
                            autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="horaEntrada">Hora entrada:</label>
                        <input required type="time" id="horaEntrada" name="horaEntrada" value="<?php echo $time ?>"
                            autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="responsavelBalanca">Responsável Balança:</label>
                        <input required type="text" id="responsavelBalanca" name="responsavelBalanca"
                            value="<?php echo $_SESSION['name'] ?>" autocomplete="off">
                    </div>
                </div>


                <input type="submit" value="Inserir Dados" class="inserirDados">
        </form>
        </fieldset>
    </div>
    <script src="../js/functions.js"></script>
    <script>
        // Seleciona os campos de CNH e Nome do Motorista
        const cnhInput = document.getElementById('cnh_motorista');
        const nomeMotoristaInput = document.getElementById('nome_motorista');

        // Adiciona um "ouvinte de evento" que será ativado quando você sair do campo CNH
        cnhInput.addEventListener('blur', () => {
            const cnh = cnhInput.value;

            // Se o campo CNH estiver vazio, não faz nada
            if (cnh.trim() === '') {
                return;
            }

            // Faz a requisição para o arquivo PHP criado no Passo 1
            fetch(`../assets/get_motorista.php?cnh_motorista=${cnh}`)
                .then(response => {
                    // Se a resposta não for OK (código 200), lança um erro
                    if (!response.ok) {
                        throw new Error('Erro na requisição.');
                    }
                    // Converte a resposta para JSON
                    return response.json();
                })
                .then(data => {
                    // Verifica se a resposta contém o nome do motorista
                    if (data.nome_motorista) {
                        // Preenche o campo Nome do Motorista com o valor recebido
                        nomeMotoristaInput.value = data.nome_motorista;
                        // --- NOVA LINHA: Torna o campo de nome somente leitura
                        nomeMotoristaInput.readOnly = true;
                    } else {
                        // Se a CNH não for encontrada, limpa o campo Nome do Motorista
                        nomeMotoristaInput.value = '';
                        // --- NOVA LINHA: Remove o atributo de somente leitura
                        nomeMotoristaInput.readOnly = false;
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    // Opcional: mostrar uma mensagem de erro para o usuário
                    alert('Erro ao buscar o nome do motorista. Tente novamente.');
                    nomeMotoristaInput.value = '';
                });
        });
        //////////////////////////////////////////////////////////////////////////////////////////

        function formatTitleCase(inputElement) {
            // 1. Salva a posição atual do cursor
            const cursorStart = inputElement.selectionStart;
            const cursorEnd = inputElement.selectionEnd;

            // 2. Limpa o valor: Remove tudo que não for letra (A-Z, a-z), espaço ou caracteres acentuados comuns.
            // Usaremos uma Regex mais abrangente para incluir acentos comuns do português.
            let value = inputElement.value;

            // Remove números e caracteres especiais (mantém letras, espaços, e a maioria dos acentos)
            // A flag 'g' garante que todas as ocorrências sejam substituídas.
            // [^a-záàâãéèêíìîóòôõúùûç\s] significa: "qualquer coisa QUE NÃO seja letra minúscula, acento ou espaço"
            // Adicionamos a flag 'i' para tornar case-insensitive (funciona para maiúsculas e minúsculas)
            value = value.replace(/[^a-záàâãéèêíìîóòôõúùûç\s]/gi, '');

            // 3. Obtém o valor limpo e o padroniza para letras minúsculas (para o Title Case)
            value = value.toLowerCase();

            // 4. Aplica a formatação Title Case
            value = value.replace(/(^|\s)\S/g, function (letter) {
                return letter.toUpperCase();
            });

            // 5. Aplica o valor formatado de volta ao campo
            inputElement.value = value;

            // 6. Restaura a posição do cursor (com ajuste caso o caractere removido estivesse antes do cursor)
            const newCursorPosition = cursorStart - (inputElement.value.length - value.length);
            inputElement.setSelectionRange(newCursorPosition, newCursorPosition);
        }
        ////////////////////////////////////////////////////////////////////////--

        function validarCNH(cnh) {
            const inputElement = document.getElementById('cnh_motorista');
            const feedback = document.getElementById('cnh_feedback'); // Mantemos o span, mas o limpamos

            // Limpa feedback anterior
            feedback.innerHTML = '';
            inputElement.classList.remove('input-error', 'input-success');

            // 1. Limpeza e Verificação Básica
            cnh = cnh.replace(/[^0-9]/g, '');

            if (cnh.length === 0) {
                return false;
            }

            if (cnh.length !== 11) {
                // Apenas para mostrar que algo está faltando, mas não é um erro de validação final
                inputElement.classList.add('input-error');
                return false;
            }

            // Verifica se todos os dígitos são iguais (inválido no BR)
            if (/^(\d)\1{10}$/.test(cnh)) {
                inputElement.classList.add('input-error');
                return false;
            }

            // --- ALGORITMO MÓDULO 11 BRASILEIRO ---
            let soma = 0;
            let peso = 9;
            for (let i = 0; i < 9; i++) {
                soma += parseInt(cnh.charAt(i)) * peso;
                peso--;
            }

            let dv1 = soma % 11;
            let dsc = 0;

            if (dv1 >= 10) {
                dv1 = 0;
                dsc = 2;
            }

            soma = 0;
            peso = 1;
            for (let i = 0; i < 9; i++) {
                soma += parseInt(cnh.charAt(i)) * peso;
                peso++;
            }

            soma -= dsc;
            let dv2 = soma % 11;

            if (dv2 >= 10) {
                dv2 = 0;
            }

            const dvCalculado = '' + dv1 + dv2;
            const dvFornecido = cnh.substr(9, 2);

            if (dvCalculado === dvFornecido) {
                // CNH VÁLIDA
                inputElement.classList.add('input-success');
                return true;
            } else {
                // CNH INVÁLIDA
                inputElement.classList.add('input-error');
                return false;
            }
        }

        ////////////////////////////////////////////////////////////%



        /**
         * Valida o formato da placa (Brasil ou Argentina Mercosul) e aplica feedback visual.
         *
         * @param {string} placa - O valor atual do campo.
         * @param {string} inputId - O ID do elemento input a ser validado.
         * @returns {boolean} True se a placa for válida, false caso contrário.
         */
        function validarPlaca(placa, inputId) {
            const inputElement = document.getElementById(inputId);

            // 1. Limpa classes de erro/sucesso anteriores para resetar o visual
            inputElement.classList.remove('input-error', 'input-success');

            // 2. Limpa e padroniza a placa: remove caracteres não alfanuméricos e converte para MAIÚSCULAS
            let placaLimpa = placa.toUpperCase().replace(/[^A-Z0-9]/g, '');

            // Aplica o valor limpo e maiúsculo de volta ao input para uniformizar
            inputElement.value = placaLimpa;

            // 3. Verifica o comprimento: A placa deve ter exatamente 7 caracteres para ser validada
            if (placaLimpa.length !== 7) {
                // Aplica o estilo de erro se o usuário começar a digitar mas não completar (feedback em tempo real)
                if (placaLimpa.length > 0) {
                    inputElement.classList.add('input-error');
                }
                return false;
            }

            // 4. EXPRESSÕES REGULARES para padrões de 7 caracteres

            // Padrão 1: Mercosul Brasileiro (LLLNLNN: 3 letras, 1 número, 1 letra, 2 números)
            const regexMercosulBR = /^[A-Z]{3}[0-9][A-Z][0-9]{2}$/;

            // Padrão 2: Antiga Brasileiro (LLLNNNN: 3 letras, 4 números)
            const regexAntigaBR = /^[A-Z]{3}[0-9]{4}$/;

            // Padrão 3: Mercosul Argentino (LLNNNLL: 2 letras, 3 números, 2 letras)
            const regexMercosulAR = /^[A-Z]{2}[0-9]{3}[A-Z]{2}$/;

            // 5. Verifica todos os padrões
            const isValida = regexMercosulBR.test(placaLimpa) ||
                regexAntigaBR.test(placaLimpa) ||
                regexMercosulAR.test(placaLimpa);

            // 6. Aplica o feedback visual final
            if (isValida) {
                inputElement.classList.add('input-success');
                return true;
            } else {
                inputElement.classList.add('input-error');
                return false;
            }
        }
    </script>
</body>

</html>