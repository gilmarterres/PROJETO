<?php
require_once("../assets/connection.php");

session_start();

$id_expedicao = null;
$dados_expedicao = [];
$mensagem = '';

if (isset($_GET['id']) && !empty($_GET['id'])) {

    $id_expedicao = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $sql_select = "SELECT id, flow, circulacao, produto, transportadora, nomeMotorista,
                   data, placaCarreta, cnhMotorista, horaEntrada, placaTanque1,
                   destino, responsavelBalanca, placaTanque2, volumeCarreta
                   FROM db_checklist.dbo.tb_marking 
                   WHERE id = :id";

    try {
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->bindParam(':id', $id_expedicao, PDO::PARAM_INT);
        $stmt_select->execute();
        $dados_expedicao = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if (!$dados_expedicao) {
            $mensagem = "TICKET" . htmlspecialchars($id_expedicao) . " não encontrado";
        }
    } catch (PDOException $e) {
        die("Erro ao carregar dados: " . $e->getMessage());
    }
} else {
    header("Location: expedition.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php
     //print_r($_SESSION);
    ?>
    <div class="container">
    <button id="bt_bk_exp">Voltar</button>
        <h3>Preenchimento do Checklist: <?php echo htmlspecialchars($dados_expedicao['ticket'] ?? ''); ?> </h3>

        <?php if ($mensagem): ?>
            <div class="message error">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <?php if ($dados_expedicao): ?>
            <table class="tableEdit">
                <thead>
                    <tr>
                        <th>circulacao</th>
                        <th>produto</th>
                        <th>transportadora</th>
                        <th>nomeMotorista</th>
                        <th>data</th>
                        <th>horaEntrada</th>
                        <th>destino</th>
                        <th>responsavelBalanca</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['circulacao'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['produto'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['transportadora'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['nomeMotorista'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['data'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['horaEntrada'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['destino'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($dados_expedicao['responsavelBalanca'] ?? '') . "</td>";
                    echo "</tr>";
                    ?>
                </tbody>
            </table>

            <div class="data-display">

                <form action="../assets/confirm_checklist.php?<?php echo htmlspecialchars($id_expedicao); ?>" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_expedicao) ?>">

                    <table>

                        <tbody>
                            <tr>
                                <td>Faróis, lanternas e setas em bom estado?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="farois" value="sim" required>Sim
                                    <input type="radio" name="farois" value="nao">Não
                                    <input type="radio" name="farois" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Vagões, válvulas e conexões isentos de vazamentos?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="vagoes" value="sim" required>Sim
                                    <input type="radio" name="vagoes" value="nao">Não
                                    <input type="radio" name="vagoes" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>No cavalo o painel de segurança e rótulo de risco meio ambiente estão conforme?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="cavalo" value="sim" required>Sim
                                    <input type="radio" name="cavalo" value="nao">Não
                                    <input type="radio" name="cavalo" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Os extintores estão com a validade da carga e teste hidrostático conformes?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="extintores" value="sim" required>Sim
                                    <input type="radio" name="extintores" value="nao">Não
                                    <input type="radio" name="extintores" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Verificado se há volume remanescente nos compartimentos da carreta a ser carregada?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="verificado" value="sim" required>Sim
                                    <input type="radio" name="verificado" value="nao">Não
                                    <input type="radio" name="verificado" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Se houver volume, drenou todas as bocas de saída?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="volume" value="sim" required>Sim
                                    <input type="radio" name="volume" value="nao">Não
                                    <input type="radio" name="volume" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Necessidade de lavar e/ou secar o tanque? (*Secar a umidade, se presente)</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="lavar" value="sim" required>Sim
                                    <input type="radio" name="lavar" value="nao">Não
                                    <input type="radio" name="lavar" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Setas de medidas de compartimento visíveis?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="setas" value="sim" required>Sim
                                    <input type="radio" name="setas" value="nao">Não
                                    <input type="radio" name="setas" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>A vedação da boca de carregamento está conforme?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="vedacao" value="sim" required>Sim
                                    <input type="radio" name="vedacao" value="nao">Não
                                    <input type="radio" name="vedacao" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Possui válvula de fundo de fecho rápido?</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="valvula" value="sim" required>Sim
                                    <input type="radio" name="valvula" value="nao">Não
                                    <input type="radio" name="valvula" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Caminhão tanque em condições de realizar o transporte</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="transporte" value="sim" required>Sim
                                    <input type="radio" name="transporte" value="nao">Não
                                    <input type="radio" name="transporte" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Certificado se os tubos de descargas (canos) irão ser carregados cheios ou vazio? </td>
                                <td class="radio-group">
                                    <label><input type="radio" name="tubos" value="sim" required>Sim
                                    <input type="radio" name="tubos" value="nao">Não
                                    <input type="radio" name="tubos" value="na">n/a</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Carregamento aprovado? Caso não, informar à supervisão.</td>
                                <td class="radio-group">
                                    <label><input type="radio" name="carregamento" value="sim" required>Sim
                                    <input type="radio" name="carregamento" value="nao">Não
                                    <input type="radio" name="carregamento" value="na">n/a</label>
                                </td>
                            </tr>

                        </tbody>


                    </table>


                    <div class="form-group">
                        <label for="operador_expedicao">Operador Expedição: </label>
                        <input required type="text" id="responsavelExpedicao" name="responsavelExpedicao"
                            value="<?php echo htmlspecialchars($_SESSION['name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="lacres">Lacres: </label>
                        <input required type="text" id="lacres" name="lacres"
                            value="">
                    </div>

                    <div class="form-group">
                        <label for="obs">Observação: </label>
                        <input type="text" id="obs" name="obs"
                            value="">
                    </div>
                    
                    <button type="submit">Confirmar Checklist</button>
                    
                
                </form>

            <?php else: ?>
                <p>Nenhum checklist disponível para preenchimento. </p>
                
            <?php endif; ?>
        </div>

</body>

</html>
<?php $conn = null; ?>
<script src="../js/functions.js"></script>