<?php
//echo "Gilmar";
require_once 'connection.php';

foreach ($_POST as $key => $value) {
    echo '<li><strong>'. $key .': '. $value .'</li>';
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $flow = 1;
    $circulacao = $_POST['circulacao'] ?? null;
    $produto = $_POST['produto'] ?? null;
    $transportadora = $_POST['transportadora'] ?? null;
    $nomeMotorista = $_POST['nomeMotorista'] ?? null;
    $data = $_POST['data'] ?? null;
    $placaCarreta = $_POST['placaCarreta'] ?? null;
    $cnhMotorista = $_POST['cnhMotorista'] ?? null;
    $horaEntrada = $_POST['horaEntrada'] ?? null;
    $placaTanque1 = $_POST['placaTanque1'] ?? null;
    $destino = $_POST['destino'] ?? null;
    $responsavelBalanca = $_POST['responsavelBalanca'] ?? null;
    $placaTanque2 = $_POST['placaTanque2'] ?? null;
    $volumeCarreta = $_POST['volumeCarreta'] ?? null;

    $sql = "INSERT INTO db_checklist.dbo.tb_marking
                (flow, circulacao, produto, transportadora, nomeMotorista, data, placaCarreta,
                 cnhMotorista, horaEntrada, placaTanque1, destino, responsavelBalanca, placaTanque2, volumeCarreta)
        VALUES (:flow, :circulacao, :produto, :transportadora, :nomeMotorista, :data, :placaCarreta,
         :cnhMotorista, :horaEntrada, :placaTanque1, :destino, :responsavelBalanca, :placaTanque2, :volumeCarreta)";

    try {
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':flow', $flow);
        $stmt->bindParam(':circulacao', $circulacao);
        $stmt->bindParam(':produto', $produto);
        $stmt->bindParam(':transportadora', $transportadora);
        $stmt->bindParam(':nomeMotorista', $nomeMotorista);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':placaCarreta', $placaCarreta);
        $stmt->bindParam(':cnhMotorista', $cnhMotorista);
        $stmt->bindParam(':horaEntrada', $horaEntrada);
        $stmt->bindParam(':placaTanque1', $placaTanque1);
        $stmt->bindParam(':destino', $destino);
        $stmt->bindParam(':responsavelBalanca', $responsavelBalanca);
        $stmt->bindParam(':placaTanque2', $placaTanque2);
        $stmt->bindParam(':volumeCarreta', $volumeCarreta);

        $stmt->execute();

        header("Location: ../pages/new_checklist.php?status=sucess");
    } catch (PDOException $e) {
        $error_message = urlencode($e->getMessage());
        header("Location: ../pages/new_checklist.php?status=error&message=" . $error_message);
        echo $error_message;
        exit();
    } finally {
        $conn = null;
    }
} else {
    header("Location: ../pages/new_checklist.php");
    exit();
}
?>