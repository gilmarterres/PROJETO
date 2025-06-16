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



if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id_to_update = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $name_us_exp = filter_input(INPUT_POST, 'name_us_exp', FILTER_SANITIZE_STRING);
    $seals = filter_input(INPUT_POST, 'seals', FILTER_SANITIZE_STRING);


if ($id_to_update && $id_to_update = $id_expedicao){
    $sql_update = "UPDATE db_checklist.dbo.tb_marking SET
                    name_us_exp = :name_us_exp,
                    seals = :seals
                    WHERE id = :id";

    try {
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bindParam(':name_us_exp', $name_us_exp, PDO::PARAM_STR);
        $stmt_update->bindParam(':seals', $seals, PDO::PARAM_STR);
        $stmt_update->bindParam(':id', $id_to_update, PDO::PARAM_MUMBER_INT);
        $stmt_update->execute();

        $dados_expedicao['name_us_exp'] = $name_us_exp;
        $dados_expedicao['seals'] = $seals;

        $_SESSION['message'] = "Sucesso!";

        header("Location: expedition.php");

        exit();
    }catch(PDOException $e){
        $mensagem = "Erro: " . $e->getMessage();
    }
}else{
    $mensagem = "Idientificação Inválida";
}

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
    <div class="container">
        <h1> Preencher dados do id ID: <?php echo htmlspecialchars($id_expedicao); ?> </h1>
    
    <?php if ($mensagem): ?>
        <div class="message error">
            <?php echo htmlspecialchars($mensagem); ?>
        </div>
        <?php endif; ?>

        <?php if ($dados_expedicao) ?>
            <div class="data-display">
                <p><span>ID:</span><?php echo htmlspecialchars ($dados_expedicao['id'] ?? ''); ?></p>
                <p><span>Fluxo:</span><?php echo htmlspecialchars ($dados_expedicao['flow'] ?? ''); ?></p>
                <p><span>Ticket:</span><?php echo htmlspecialchars ($dados_expedicao['ticket'] ?? ''); ?></p>
                <p><span>Responsável Balança:</span><?php echo htmlspecialchars ($dados_expedicao['name_us_bal'] ?? ''); ?></p>
                <p><span>Placa:</span><?php echo htmlspecialchars ($dados_expedicao['plate'] ?? ''); ?></p>
                <p><span>Motorista:</span><?php echo htmlspecialchars ($dados_expedicao['driver'] ?? ''); ?></p>
                <p><span>Responsável Expedição:</span><?php echo htmlspecialchars ($dados_expedicao['name_us_exp'] ?? ''); ?></p>
                <p><span>Lacres:</span><?php echo htmlspecialchars ($dados_expedicao['seals'] ?? ''); ?></p>
            </div>

        <form action="edit_cheklist.php?<?php echo htmlspecialchars($id_expedicao); ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_expedicao) ?>">

        <div class="form-group">
            
    
    
    
    </div>

    
</body>
</html>