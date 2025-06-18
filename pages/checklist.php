<?php
    require_once("../assets/connection.php");

    session_start();

    $id_expedicao = null;
    $dados_expedicao = [];
    $mensagem = '';

if (isset($_GET['id']) && !empty($_GET['id'])){
        $id_expedicao = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        $sql_select = "SELECT id, flow, ticket, name_us_bal, plate, driver, name_us_exp, seals FROM db_checklist.dbo.tb_marking";

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
        header("Location: consult_checklist.php");
        exit();
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist</title>
</head>
<body>
    
</body>
</html>