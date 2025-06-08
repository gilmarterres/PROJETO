<?php

$serverName = "LOCALHOST\\SQLEXPRESS";
$connectionOptions = array(
    "Database" => "db_checklist",
    "Uid" => "sa",
    "PWD" => "123456"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $login = $_POST['login'] ?? '';
    $password = $_POST['senha'] ?? '';

    $sql = "SELECT id, Login, password FROM tb_users WHERE Login = ?";
    $params = array($login);

    $$stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false){
        echo "Erro na consulta.<br />";
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_arrays($stmt, SQLSRV_FETCH_ASSOC);

    if ($row){
        if($password === $row['password']){
            echo "Sucesso";
        }else{
            echo "Login ou senha inválidos";
        }
    }else{
        echo "Login ou senha inválidos.";
    }
    
    sqlsrv_free_stmt($stmt);
}else{
    echo "Erro no formulário.";
}

sqlsrv_close($conn);




?>