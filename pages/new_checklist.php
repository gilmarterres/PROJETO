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
                        <input type="text" id="circulacao" name="circulacao">
                    </div>
                    <div class="form-group">
                        <label for="produto">Produto:</label>
                        <input type="text" id="produto" name="produto">
                    </div>
                    <div class="form-group">
                        <label for="transportadora">Transportadora:</label>
                        <input type="text" id="transportadora" name="transportadora">
                    </div>
                    <div class="form-group">
                        <label for="nomeMotorista">Nome do motorista:</label>
                        <input type="text" id="nomeMotorista" name="nomeMotorista">
                    </div>
                    <div class="form-group">
                        <label for="data">Data:</label>
                        <input type="date" id="data" name="data" value="<?php echo $today ?>">
                    </div>
                    <div class="form-group">
                        <label for="placaCarreta">Placa da carreta:</label>
                        <input type="text" id="placaCarreta" name="placaCarreta">
                    </div>
                    <div class="form-group">
                        <label for="cnhMotorista">CNH motorista:</label>
                        <input type="text" id="cnhMotorista" name="cnhMotorista">
                    </div>
                    <div class="form-group">
                        <label for="horaEntrada">Hora entrada:</label>
                        <input type="time" id="horaEntrada" name="horaEntrada" value="<?php echo $time ?>">
                    </div>
                    <div class="form-group">
                        <label for="placaTanque1">Placa Tanque 1:</label>
                        <input type="text" id="placaTanque1" name="placaTanque1">
                    </div>
                    <div class="form-group">
                        <label for="placaTanque2">Placa Tanque 2:</label>
                        <input type="text" id="placaTanque2" name="placaTanque2">
                    </div>
                    <div class="form-group">
                        <label for="destino">Destino:</label>
                        <input type="text" id="destino" name="destino">
                    </div>
                    <div class="form-group">
                        <label for="responsavelBalanca">Responsável Balança:</label>
                        <input type="text" id="responsavelBalanca" name="responsavelBalanca" value="<?php echo $_SESSION['name'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="volumeCarreta">Volume da carreta:</label>
                        <input type="text" id="volumeCarreta" name="volumeCarreta">
                    </div>
                </div>
              

            <input type="submit" value="Inserir Dados">
        </form>
        </fieldset>
    </div>
    <script src="../js/functions.js"></script>
</body>

</html>