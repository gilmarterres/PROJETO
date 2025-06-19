<?php
require_once("../assets/connection.php");

$sql = "SELECT id, flow, ticket, name_us_bal, plate, driver, name_us_exp, seals FROM db_checklist.dbo.tb_marking WHERE flow = 1";

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
    session_start();

    if (isset($_SESSION['message'])) {
        echo "<script>";
        echo "alert('Dados inseridos com sucesso!');";
        echo "</script>";
        unset($_SESSION['message']);
    }

    //print_r($_SESSION);
    ?>

    <?php if ($totalRows > 0) { ?>
        <table>
            <tr>
                <th>Ticket</th>
                <th>Responsável Balança</th>
                <th>Placa</th>
                <th>Motorista</th>
            </tr>

            </thead>
            <tbody>
                <?php

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td><a href=\"edit_checklist.php?id=" . htmlspecialchars($row['id'] ?? '') . "\">" . htmlspecialchars($row['ticket'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['name_us_bal'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['plate'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['driver'] ?? '') . "</td>";
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