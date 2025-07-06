<?php

session_start();

$serverName = "LOCALHOST\\SQLEXPRESS";
$connectionOptions = array(
    "Database" => "db_checklist",
    "Uid" => "sa",
    "PWD" => "123456"
);

try {
    $conn = new PDO(
        "sqlsrv:Server=$serverName;Database={$connectionOptions['Database']}",
        $connectionOptions['Uid'],
        $connectionOptions['PWD']
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $username = $_POST['login'] ?? '';
    $password = $_POST['senha'] ?? '';

    $sql = "SELECT id, name, login, password, accesslevel FROM tb_users WHERE login = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);

    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user){
        if ($password === $user['password']){
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['login'];
            $_SESSION['accesslevel'] = $user['accesslevel'];
            $_SESSION['name'] = $user['name'];

            echo "Login bem sucedido! Bem vindo, " . $_SESSION['username'] . " ID: " . $_SESSION['accesslevel'];

            if($_SESSION['accesslevel'] == 0){                
                header("Location: ../pages/admin.php");
            }else if($_SESSION['accesslevel'] == 1){
                header("Location: ../pages/balanca.php");
            }else if($_SESSION['accesslevel'] == 2){
                header("Location: ../pages/expedicao.php");
            }else{
                header("Location: ../index.html");
            }

        }else{
            echo "Senha incorreta.";
        }
    }else{
        echo "Usuário não encontrado.";
    }
} catch(PDOException $e){
    die("Falha na conexão:" . $e->getMessage());
}catch(Exception $e){
    die("Erro: " . $e->getMessage());
}

?>