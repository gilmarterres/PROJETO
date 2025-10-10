<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        * {
            box-sizing: border-box;
        }

        .index {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #bbb;
        }

        form {

            width: 300px;
            margin: 100px;
            text-align: center;
        }

        input {

            display: block;
            margin: 10px auto;
            width: 250px;
            height: 30px;
        }
    </style>

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
</head>

<body class="index">
    <form action="assets/login.php" method="POST">
        <h1>Login</h1>
        <input type="text" placeholder="login" id="login" name="login" autocomplete="off">
        <input type="password" placeholder="senha" id="senha" name="senha" autocomplete="off">
        <input class="bt-logar" type="submit" value="Logar">
    </form>
</body>

</html>