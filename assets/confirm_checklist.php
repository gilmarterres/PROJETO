<?php
    require_once("../assets/connection.php");

    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    echo "Gilmar";
    $id_to_update = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $name_us_exp = filter_input(INPUT_POST, 'name_us_exp', FILTER_UNSAFE_RAW);
    $seals = filter_input(INPUT_POST, 'seals', FILTER_UNSAFE_RAW);
    $flow = 2;

    if ($id_to_update){
        $sql_update = "UPDATE db_checklist.dbo.tb_marking SET
                        name_us_exp = :name_us_exp,
                        flow = :flow,
                        seals = :seals
                        WHERE id = :id";

        try {
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bindParam(':name_us_exp', $name_us_exp, PDO::PARAM_STR);
            $stmt_update->bindParam(':flow', $flow, PDO::PARAM_INT);
            $stmt_update->bindParam(':seals', $seals, PDO::PARAM_STR);
            $stmt_update->bindParam(':id', $id_to_update, PDO::PARAM_INT);
            $stmt_update->execute();

            $dados_expedicao['name_us_exp'] = $name_us_exp;
            $dados_expedicao['seals'] = $seals;

            $_SESSION['message'] = "sucess";

            header("Location: ../pages/expedition.php");
            echo "Sucesso!!";

            exit();
        }catch(PDOException $e){
            $mensagem = "Erro: " . $e->getMessage();
        }
    }else{
        $mensagem = "Idientificação Inválida";
    }

}
?>

</body>
</html>