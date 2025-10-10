<?php
require_once("../assets/connection.php");

$sql = "SELECT id, flow, ticket, produto, transportadora, nomeMotorista, data, placaCavalo,
                 cnhMotorista, horaEntrada, placaTanque1, destino, responsavelBalanca,
                 placaTanque2, volumeCarreta FROM db_checklist.dbo.tb_marking WHERE flow = 1";

try {
    $stmt = $conn->query($sql);
} catch (PDOException $e) {
    die("Erro na execução. " . $e->getMessage());
}
////////////////////////////////////////
///contar linhas:

$countSql = "SELECT COUNT(*) AS total_rows FROM db_checklist.dbo.tb_marking WHERE flow = 1";
try {
    $countStmt = $conn->query($countSql);
    $totalRows = $countStmt->fetch(PDO::FETCH_ASSOC)['total_rows'];
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}

////////////////////////////////////////
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expedição</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <button id="bt_lg_screen">Voltar</button>
    <?php
    //session_start();

    //if (isset($_SESSION['message'])) {
    //    echo "<script>";
    //    echo "alert('Dados inseridos com sucesso!');";
    //   echo "</script>";
    //   unset($_SESSION['message']);
    //}

    //print_r($_SESSION);
    ?>

    <?php if ($totalRows > 0) { ?>
        <table>
            <tr>
                <th>ticket</th>
                <th>produto</th>
                <th>transportadora</th>
                <th>nomeMotorista</th>
                <th>data</th>
                <th>horaEntrada</th>
                <th>destino</th>
                <th>responsavelBalanca</th>
            </tr>

            </thead>
            <tbody>
                <?php

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td><a href=\"edit_checklist.php?id=" . htmlspecialchars($row['id'] ?? '') . "\">" . htmlspecialchars($row['ticket'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['produto'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['transportadora'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['nomeMotorista'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['data'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['horaEntrada'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['destino'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['responsavelBalanca'] ?? '') . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

    <?php } else { ?>
        <h1>Nenhum checklist encontrado para preenchimento!</h1>
    <?php } ?>


    <?php $conn = null; ?>
    <script src="../js/functions.js"></script>
</body>

</html>