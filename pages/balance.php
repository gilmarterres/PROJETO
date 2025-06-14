<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

<style>
body{
    text-align: center;
}

</style>

</head>
<body>
    <?php
        session_start();
        print_r($_SESSION);
    ?>
    <br>
    <button id="bt_lg_screen">Voltar</button>

    <h3>BALANÇA</h3>

    <button id="bt_new">Criar novo checklist</button>

    <button id="bt_consult">Consultar checklist</button>



<script src="../js/functions.js"></script>
</body>
</html>