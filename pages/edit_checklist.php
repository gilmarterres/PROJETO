<?php
    require_once("../assets/connection.php");

    session_start();

    $id_expedicao = null;
    $dados_expedicao = [];
    $mensagem = '';

if (isset($_GET['id']) && !empty($_GET['id'])){
    
        $id_expedicao = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        $sql_select = "SELECT id, flow, ticket, name_us_bal, plate, driver, name_us_exp, seals FROM db_checklist.dbo.tb_marking WHERE id = :id";

        try{
            $stmt_select = $conn->prepare($sql_select);
            $stmt_select->bindParam(':id', $id_expedicao, PDO::PARAM_INT);
            $stmt_select->execute();
            $dados_expedicao = $stmt_select->fetch(PDO::FETCH_ASSOC);

            if(!$dados_expedicao){
                $mensagem = "TICKET" . htmlspecialchars($id_expedicao) . " não encontrado";  
            }
        }catch (PDOException $e){
            die("Erro ao carregar dados: " . $e->getMessage());
        }
    }else{
        header("Location: expedition.php");
        exit();
    }

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

<style>

 
 
</style>

</head>
<body>

        
    <?php
       // print_r($_SESSION);
    ?>
    <div class="container">
        <h1> Preencher dados do id ID: <?php echo htmlspecialchars($id_expedicao); ?> </h1>
    
    <?php if ($mensagem): ?>
        <div class="message error">
            <?php echo htmlspecialchars($mensagem); ?>
        </div>
    <?php endif; ?>

        <?php if ($dados_expedicao): ?>
            <div class="data-display">
                <p><span>ID:</span><?php echo htmlspecialchars ($dados_expedicao['id'] ?? ''); ?></p>
                <p><span>Fluxo:</span><?php echo htmlspecialchars ($dados_expedicao['flow'] ?? ''); ?></p>
                <p><span>Ticket:</span><?php echo htmlspecialchars ($dados_expedicao['ticket'] ?? ''); ?></p>
                <p><span>Responsável Balança:</span><?php echo htmlspecialchars ($dados_expedicao['name_us_bal'] ?? ''); ?></p>
                <p><span>Placa:</span><?php echo htmlspecialchars ($dados_expedicao['plate'] ?? ''); ?></p>
                <p><span>Motorista:</span><?php echo htmlspecialchars ($dados_expedicao['driver'] ?? ''); ?></p>
            </div>

        <form action="../assets/confirm_checklist.php?<?php echo htmlspecialchars($id_expedicao); ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_expedicao) ?>">

        <div class="form-group">
            <label for="operador_expedicao">Operador Expedição: </label>
            <input required type="text" id="name_us_exp" name="name_us_exp"
                value="<?php echo htmlspecialchars($_SESSION['username']?? '') ?>">
        </div>

        <div class="form-group">
            <label for="lacres">Lacres: </label>
            <input required type="text" id="seals" name="seals"
                value="<?php echo htmlspecialchars($dados_expedicao['seals'] ?? '') ?>">
        </div>

        <button type="submit">Confirmar Checklist</button>
        <a href="expedition.php" class="back-button">Voltar para Lista</a>
    </form>

    <?php else: ?>
        <p>Nenhum checklist disponível para preenchimento. </p>
        <a href="expedition.php" class="back-button">Voltar para Lista</a>
    <?php endif; ?>
    </div>

</body>
</html>
<?php $conn = null; ?>