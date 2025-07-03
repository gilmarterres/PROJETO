<?php
require_once("../assets/connection.php");

$sql = "SELECT id, flow, circulacao, produto, transportadora, nomeMotorista, data, placaCarreta,
                 cnhMotorista, horaEntrada, placaTanque1, destino, responsavelBalanca,
                 placaTanque2, volumeCarreta, responsavelExpedicao FROM db_checklist.dbo.tb_marking
                 WHERE flow = 2
                 ";

try {
    $stmt = $conn->query($sql);
} catch (PDOException $e) {
    die("Erro na execução. " . $e->getMessage());
}

////////////////////////////////////////
///contar linhas:

$countSql = "SELECT COUNT(*) AS total_rows FROM db_checklist.dbo.tb_marking";
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
    <title>Consulta Checklist</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <button id="bt_back">Voltar</button>

    <?php if ($totalRows > 0) { ?>
        <h1>Consulta Checklist</h1>
        <table>
            <thead>
                <tr>
                    <th>circulacao</th>
                    <th>produto</th>
                    <th>transportadora</th>
                    <th>nome Motorista</th>
                    <th>data</th>
                    <th>hora Entrada</th>
                    <th>destino</th>
                    <th>responsave lBalanca</th>
                    <th>responsavel Expedicao</th>
                </tr>
            </thead>
            <tbody>
                <?php

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td><a href=\"checklist.php?id=" . htmlspecialchars($row['id'] ?? '') . "\">" . htmlspecialchars($row['circulacao'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['produto'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['transportadora'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['nomeMotorista'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['data'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['horaEntrada'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['destino'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['responsavelBalanca'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['responsavelExpedicao'] ?? '') . "</td>";
                    echo "</tr>";
                }

                ?>
            </tbody>
        </table>

    <?php } else { ?>
        <h1>Nenhum checklist encontrado para consulta!</h1>
    <?php } ?>

    <?php $conn = null; ?>
    <script src="../js/functions.js"></script>
</body>

</html>