<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Checklist</title>
</head>
<body>
    Consulta Checklist

    <?php
        $serverName = "LOCALHOST\\SQLEXPRESS";
        $databaseName = "db_checklist";
        $Uid = "sa";
        $PWE = "123456";

        try {
            $conn = new PDO("sqlsrv:Server=$serverName;Database=$databaseName", $Uid, $PWE);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $e){
            die("Erro na conexão: " . $e->getMessage());
        }

        $sql = "SELECT id, flow, name_us_bal, plate, driver, name_us_exp, seals FROM db_checklist.dbo.tb_marking";

        try{
            $stmt = $conn->query($sql);
        }catch (PDOException $e){
            die ("Erro na execução. " . $e->gerMessage());
        }
    ?>














<?php
    $conn = null;
?>
</body>
</html>