<?php
$serverName = "LOCALHOST\\SQLEXPRESS";
$databaseName = "db_checklist";
$uid = "sa";
$pwe = "123456";

try {
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$databaseName", $uid, $pwe);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>