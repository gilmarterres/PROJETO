<?php
//echo "Gilmar";
require_once 'connection.php';

foreach ($_POST as $key => $value) {
    echo '<li><strong>' . $key . ': ' . $value . '</li>';
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $flow = 1;
    $ticket = $_POST['ticket'] ?? null;
    $produto = $_POST['produto'] ?? null;
    $transportadora = $_POST['transportadora'] ?? null;
    $nomeMotorista = $_POST['nomeMotorista'] ?? null;
    $data = $_POST['data'] ?? null;
    $placaCavalo = $_POST['placaCavalo'] ?? null;
    $cnhMotorista = $_POST['cnhMotorista'] ?? null;
    $horaEntrada = $_POST['horaEntrada'] ?? null;
    $placaTanque1 = $_POST['placaTanque1'] ?? null;
    $destino = $_POST['destino'] ?? null;
    $responsavelBalanca = $_POST['responsavelBalanca'] ?? null;
    $placaTanque2 = $_POST['placaTanque2'] ?? null;
    $volumeCarreta = $_POST['volumeCarreta'] ?? null;


    try {

        // Inicia a transação para garantir a integridade dos dados
        $conn->beginTransaction();

        // 1. Validação: Verifica se a CNH do motorista já existe na tabela 'tb_motorista'
        $sql_check_cnh = "SELECT COUNT(*) FROM tb_motorista WHERE cnh_motorista = :cnh_motorista";
        $stmt_check = $conn->prepare($sql_check_cnh);
        $stmt_check->bindParam(':cnh_motorista', $cnhMotorista);
        $stmt_check->execute();
        $cnh_existe = $stmt_check->fetchColumn();

        // Se a CNH não existir, insere o novo motorista
        if ($cnh_existe == 0) {
            $sql_motorista = "INSERT INTO tb_motorista (nome_motorista, cnh_motorista) VALUES (:nome_motorista, :cnh_motorista)";
            $stmt_motorista = $conn->prepare($sql_motorista);
            $stmt_motorista->bindParam(':nome_motorista', $nomeMotorista);
            $stmt_motorista->bindParam(':cnh_motorista', $cnhMotorista);
            $stmt_motorista->execute();
        }

        // 2. Insere na tabela 'tb_marking' (esta inserção sempre ocorrerá)
        $sql_marking = "INSERT INTO db_checklist.dbo.tb_marking
                        (flow, ticket, produto, transportadora, nomeMotorista, data, placaCavalo,
                         cnhMotorista, horaEntrada, placaTanque1, destino, responsavelBalanca, placaTanque2, volumeCarreta)
                        VALUES (:flow, :ticket, :produto, :transportadora, :nomeMotorista, :data, :placaCavalo,
                        :cnhMotorista, :horaEntrada, :placaTanque1, :destino, :responsavelBalanca, :placaTanque2, :volumeCarreta)";
        $stmt_marking = $conn->prepare($sql_marking);

        $stmt_marking->bindParam(':flow', $flow);
        $stmt_marking->bindParam(':ticket', $ticket);
        $stmt_marking->bindParam(':produto', $produto);
        $stmt_marking->bindParam(':transportadora', $transportadora);
        $stmt_marking->bindParam(':nomeMotorista', $nomeMotorista);
        $stmt_marking->bindParam(':data', $data);
        $stmt_marking->bindParam(':placaCavalo', $placaCavalo);
        $stmt_marking->bindParam(':cnhMotorista', $cnhMotorista);
        $stmt_marking->bindParam(':horaEntrada', $horaEntrada);
        $stmt_marking->bindParam(':placaTanque1', $placaTanque1);
        $stmt_marking->bindParam(':destino', $destino);
        $stmt_marking->bindParam(':responsavelBalanca', $responsavelBalanca);
        $stmt_marking->bindParam(':placaTanque2', $placaTanque2);
        $stmt_marking->bindParam(':volumeCarreta', $volumeCarreta);
        $stmt_marking->execute();

        // Confirma a transação se tudo der certo
        $conn->commit();

        header("Location: ../pages/new_checklist.php?status=sucess");
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
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
