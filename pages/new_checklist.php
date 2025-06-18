<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo checklist</title>
</head>
<body>
    <?php
        session_start();
        //print_r($_SESSION);
        //print_r($_GET);
    ?>
    <br>
    <button id="bt_back">Voltar</button>
        <div class="container">

        <h1>Novo Checklist</h1>

        <?php
        if (isset($_GET['status'])){
            if ($_GET['status'] == 'sucess'){
                echo "<script>";
                echo "alert('Dados inseridos com sucesso!');";
                echo "</script>";
            }elseif ($_GET['status'] == 'error'){
                $error_message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Ocorreu um erro ao inserir os dados.';
                echo '<div class="message error">' . $error_message . '</div>';
            }
        }
        ?>
        
        <form action="../assets/insert_checklist.php" method="post">
            <label for="ticket">Ticket:</label>
            <input type="text" id="ticket" name="ticket" required><br>

            <label for="name_us_bal">Responsável Balança:</label>
            <input type="text" id="name_us_bal" name="name_us_bal" value="<?php echo $_SESSION['username'] ?? '' ?>" required><br>

            <label for="plate">Placa:</label>
            <input type="text" id="plate" name="plate" required><br>

            <label for="driver">Nome do Motorista:</label>
            <input type="text" id="driver" name="driver" required><br>

            <input type="submit" value="Inserir Dados">
    </form>
        </div>
<script src="../js/functions.js"></script>
</body>
</html>