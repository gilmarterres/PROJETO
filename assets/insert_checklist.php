<?php
//echo "Gilmar";
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == "POST"){
    $ticket = $_POST['ticket'] ?? null;
    $name_us_bal = $_POST['name_us_bal'] ?? null;
    $plate = $_POST['plate'] ?? null;
    $driver = $_POST['driver'] ?? null;


$flow = 1;

$sql = "INSERT INTO db_checklist.dbo.tb_marking
        (flow, ticket, name_us_bal, plate, driver)
        VALUES (:flow, :ticket, :name_us_bal, :plate, :driver)
        ";

try{
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':flow',$flow);
    $stmt->bindParam(':ticket',$ticket);
    $stmt->bindParam(':name_us_bal',$name_us_bal);
    $stmt->bindParam(':plate',$plate);
    $stmt->bindParam(':driver',$driver);

    $stmt->execute();

    header("Location: ../pages/new_checklist.php?status=sucess");
}catch (PDOException $e){
    $error_message = urlencode($e->getMessage());
    header("Location: ../pages/new_checklist.php?status=error&message=" . $error_message);
    exit();
}finally{
    $conn = null;
}
}else{
    header("Location: ../pages/new_checklist.php");
    exit();
}
?>