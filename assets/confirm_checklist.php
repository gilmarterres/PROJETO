<?php
require_once("../assets/connection.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST";

    $id_to_update = filter_input(INPUT_POST, 'id', FILTER_UNSAFE_RAW);
    $farois = filter_input(INPUT_POST, 'farois', FILTER_UNSAFE_RAW);
    $vagoes = filter_input(INPUT_POST, 'vagoes', FILTER_UNSAFE_RAW);
    $cavalo = filter_input(INPUT_POST, 'cavalo', FILTER_UNSAFE_RAW);
    $extintores = filter_input(INPUT_POST, 'extintores', FILTER_UNSAFE_RAW);
    $verificado = filter_input(INPUT_POST, 'verificado', FILTER_UNSAFE_RAW);
    $lavar = filter_input(INPUT_POST, 'lavar', FILTER_UNSAFE_RAW);
    $vedacao = filter_input(INPUT_POST, 'vedacao', FILTER_UNSAFE_RAW);
    $valvula = filter_input(INPUT_POST, 'valvula', FILTER_UNSAFE_RAW);
    $transporte = filter_input(INPUT_POST, 'transporte', FILTER_UNSAFE_RAW);
    $tubos = filter_input(INPUT_POST, 'tubos', FILTER_UNSAFE_RAW);    
    $carregamento = filter_input(INPUT_POST, 'carregamento', FILTER_UNSAFE_RAW);
    $responsavelExpedicao = filter_input(INPUT_POST, 'responsavelExpedicao', FILTER_UNSAFE_RAW);
    $lacres = filter_input(INPUT_POST, 'lacres', FILTER_UNSAFE_RAW);
    $obs = filter_input(INPUT_POST, 'obs', FILTER_UNSAFE_RAW);    
    $flow = 2;

    if ($id_to_update) {
        $sql_update = "UPDATE db_checklist.dbo.tb_marking SET
                        farois = :farois,
                        vagoes = :vagoes,
                        cavalo = :cavalo,
                        extintores = :extintores,
                        verificado = :verificado,
                        lavar = :lavar,
                        vedacao = :vedacao,
                        valvula = :valvula,
                        transporte = :transporte,
                        tubos = :tubos,
                        carregamento = :carregamento,
                        responsavelExpedicao = :responsavelExpedicao,
                        lacres = :lacres,
                        obs = :obs,
                        flow = :flow
                        WHERE id = :id";

        try {
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bindParam(':id', $id_to_update, PDO::PARAM_INT);
            $stmt_update->bindParam(':flow', $flow, PDO::PARAM_INT);
            $stmt_update->bindParam(':farois', $farois, PDO::PARAM_STR);
            $stmt_update->bindParam(':vagoes', $vagoes, PDO::PARAM_STR);
            $stmt_update->bindParam(':cavalo', $cavalo, PDO::PARAM_STR);
            $stmt_update->bindParam(':extintores', $extintores, PDO::PARAM_STR);
            $stmt_update->bindParam(':verificado', $verificado, PDO::PARAM_STR);
            $stmt_update->bindParam(':lavar', $lavar, PDO::PARAM_STR);
            $stmt_update->bindParam(':vedacao', $vedacao, PDO::PARAM_STR);
            $stmt_update->bindParam(':valvula', $valvula, PDO::PARAM_STR);
            $stmt_update->bindParam(':transporte', $transporte, PDO::PARAM_STR);
            $stmt_update->bindParam(':tubos', $tubos, PDO::PARAM_STR);
            $stmt_update->bindParam(':carregamento', $carregamento, PDO::PARAM_STR);
            $stmt_update->bindParam(':responsavelExpedicao', $responsavelExpedicao, PDO::PARAM_STR);
            $stmt_update->bindParam(':lacres', $lacres, PDO::PARAM_STR);
            $stmt_update->bindParam(':obs', $obs, PDO::PARAM_STR);
            
            $stmt_update->execute();

            //$dados_expedicao['name_us_exp'] = $name_us_exp;
            //$dados_expedicao['seals'] = $seals;

            $_SESSION['message'] = "sucess";

            header("Location: ../pages/expedicao.php");
            echo "Sucesso!!";

            exit();
        } catch (PDOException $e) {
            $mensagem = "Erro: " . $e->getMessage();
            echo $mensagem;
        }
    } else {
        $mensagem = "Idientificação Inválida";
    }

}
?>