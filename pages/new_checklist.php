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
                        <input required type="text" id="ticket" name="ticket" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="produto">Produto:</label>
                        <input required type="text" id="produto" name="produto" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="transportadora">Transportadora:</label>
                        <input required type="text" id="transportadora" name="transportadora" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <!-- <label for="cnhMotorista">CNH motorista:</label> -->
                        <!-- <input required type="text" id="cnhMotorista" name="cnhMotorista" autocomplete="off"> -->

                        <label for="cnh_motorista">CNH motorista:</label>
                        <input type="text" id="cnh_motorista" name="cnhMotorista">
                    </div>
                    <div class="form-group">
                        <!-- <label for="nomeMotorista">Nome do motorista:</label> -->
                        <!-- <input required type="text" id="nomeMotorista" name="nomeMotorista" autocomplete="off"> -->

                        <label for="nome_motorista">Nome do motorista:</label>
                        <input type="text" id="nome_motorista" name="nomeMotorista">
                    </div>
                    <div class="form-group">
                        <label for="data">Data:</label>
                        <input required type="date" id="data" name="data" value="<?php echo $today ?>" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="placaCavalo">Placa do cavalo:</label>
                        <input required type="text" id="placaCavalo" name="placaCavalo" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="horaEntrada">Hora entrada:</label>
                        <input required type="time" id="horaEntrada" name="horaEntrada" value="<?php echo $time ?>" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="placaTanque1">Placa Tanque 1:</label>
                        <input required type="text" id="placaTanque1" name="placaTanque1" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="placaTanque2">Placa Tanque 2:</label>
                        <input required type="text" id="placaTanque2" name="placaTanque2" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="destino">Destino:</label>
                        <input required type="text" id="destino" name="destino" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="responsavelBalanca">Responsável Balança:</label>
                        <input required type="text" id="responsavelBalanca" name="responsavelBalanca" value="<?php echo $_SESSION['name'] ?>" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="volumeCarreta">Volume da carreta:</label>
                        <input required type="text" id="volumeCarreta" name="volumeCarreta" autocomplete="off">
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
    </script>
</body>

</html>