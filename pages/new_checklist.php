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
                        <label for="circulacao">N° Circulação:</label>
                        <input required type="text" id="circulacao" name="circulacao" autocomplete="off">
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
                        <label for="nomeMotorista">Nome do motorista:</label>
                        <input required type="text" id="nomeMotorista" name="nomeMotorista" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="data">Data:</label>
                        <input required type="date" id="data" name="data" value="<?php echo $today ?>" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="placaCarreta">Placa da carreta:</label>
                        <input required type="text" id="placaCarreta" name="placaCarreta" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="cnhMotorista">CNH motorista:</label>
                        <input required type="text" id="cnhMotorista" name="cnhMotorista" autocomplete="off">
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
</body>

</html>