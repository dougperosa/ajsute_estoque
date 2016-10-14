<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta name="author" content="Douglas Perosa"/>

        <link rel="shortcut icon" href="imagens/favicon.ico"/>

        <link href="bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>

        <meta charset="UTF-8">
        <title>Ajuste de Estoque</title>
    </head>
    <body class="container">
        <div class="container" style="height: 150px; background-color: #00995D; border-radius: 6px;">
            <table width="100%" border="0">
                <tr>
                    <td width="26%"><a href=""><img src="./imagens/logo.png" id="logo_cabecalho" border="0" style="z-index: 1; width: 187px; height: 115px; margin-left: 30px; margin-top: 5px "></a></td>
                </tr>
            </table>
        </div>
        <br>
        <form action="verifica_movimentacao.php" method="post" style="background-color: #F5F5F5; border-radius: 10px">
            <div style="margin-left: 25px;">
                <br>
                <span>Informe o(s) ID(s) dos Itens:</span>
                <input type="text" name="item" id="item" style="width: 550px" required><br>
                <span>Data Inicial:</span>
                <input type="date" name="data" id="data" required><br>
                <br>
                <button type="submit" class="btn btn-success" style="width: 150px">Iniciar</button>
            </div>
            <br><br>
        </form>
    </body>
</html>
