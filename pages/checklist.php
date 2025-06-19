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
        //header("Location: consult_checklist.php");
        //exit();
    }

    

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist</title>
</head>

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

</body>
</html>
<?php $conn = null; ?>