<?php
    require_once("../assets/connection.php");

        $sql = "SELECT id, flow, ticket, name_us_bal, plate, driver, name_us_exp, seals FROM db_checklist.dbo.tb_marking";

        try{
            $stmt = $conn->query($sql);
        }catch (PDOException $e){
            die ("Erro na execução. " . $e->gerMessage());
        }
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
        }
        h1{
            color:#333;
            border: 0px solid #000;
            text-align: center
        }
        table{
            border: 1px solid #000;
            width: 100%;
            border-collapse: collapse;
            margin-top:20px;
            box-shadow: 0 0 10px rgb(0, 0, 0, 0.1);
            background-color: #fff;
        }
        th, td{
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th{
            background-color: #007bff;
            color: white;
            text-transform: upercase;
        }
        tr:nth-child(even){
            background-color: #f9f9f9;
        }
        tr:hover{
            background-color: #f1f1f1;
        }
        
    </style>
</head>
<body>
<button id="bt_lg_screen">Voltar</button>
<?php
        session_start();
        print_r($_SESSION);
?>
<h3>EXPEDICAO</h3>
<table>
        <tr>
            <th>ID</th>
            <th>Fluxo</th>
            <th>Ticket</th>
            <th>Nome Balanceiro</th>
            <th>Placa</th>
            <th>Motorista</th>
            <th>Operador Expedição</th>
            <th>Lacres</th>
        </tr>
    
</thead>
<tbody>
        <?php

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['flow'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['ticket'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['name_us_bal'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['plate'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['driver'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['name_us_exp'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['seals'] ?? '') . "</td>";
            echo "</tr>";
        }

        ?>
</tbody>
</table>




<?php $conn = null; ?>
<script src="../js/functions.js"></script>
</body>
</html>