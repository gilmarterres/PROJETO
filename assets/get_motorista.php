<?php
// Inclua o arquivo de conexão com o banco de dados
require_once 'connection.php';

// Verifique se a CNH foi enviada via GET
if (isset($_GET['cnh_motorista']) && !empty($_GET['cnh_motorista'])) {
    $cnhMotorista = $_GET['cnh_motorista'];

    // Consulta SQL para buscar o nome do motorista pela CNH
    $sql = "SELECT nome_motorista FROM tb_motorista WHERE cnh_motorista = :cnh_motorista";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cnh_motorista', $cnhMotorista);
        $stmt->execute();

        $motorista = $stmt->fetch(PDO::FETCH_ASSOC);

        // Define o cabeçalho para responder em JSON
        header('Content-Type: application/json');

        // Se o motorista for encontrado, retorna o nome. Caso contrário, retorna null.
        if ($motorista) {
            echo json_encode(['nome_motorista' => $motorista['nome_motorista']]);
        } else {
            echo json_encode(['nome_motorista' => null]);
        }
    } catch (PDOException $e) {
        // Em caso de erro, retorna uma resposta de erro em JSON
        header('Content-Type: application/json');
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Erro ao consultar o banco de dados: ' . $e->getMessage()]);
    }
} else {
    // Se a CNH não foi enviada, retorna um erro
    header('Content-Type: application/json');
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'CNH não fornecida.']);
}

// Fechar a conexão
$conn = null;
