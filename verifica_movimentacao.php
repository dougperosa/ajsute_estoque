<?php

//CONECTA COM BANCO DE DADOS
include './conectorBD.php';

$conexao = new conexao();
$conexao->conecta();

$itens = $_POST['item']; //PEGA O VALOR DO ITEM(S) INFORMADO NO CAMPO DO FORMULÁRIO
$data_inicio = $_POST['data'];

//SELECT DOS ITENS INFORMADOS
$query = 'select ID, ID_ITEM, QUANTIDADE, OPERACAO, SALDO, date_format(DATA,"%d/%m/%Y") from UNI_ESTOQUE where DATA >= "' . $data_inicio . '" AND ID_ITEM IN (' . $_POST['item'] . ') order by ID_ITEM, ID';
$resultset = mysql_query($query);

//VARIÁVEIS PARA VERIFICAÇÕES
$quant_anterior = null;
$saldo_correto = null;
$erro = false;
$item = null;

//PERCORRE TODAS AS LINHAS DO SELECT
while ($row = mysql_fetch_array($resultset)) {
//VERIFICACAO PARA OS CASOS EM QUE FOR INFORMADO MAIS DE UM ITEM, ASSIM ELE IDENTIFICA QUANDO TERMINA A VERIFICAÇÃO DE UM E COMEÇA A VERIFICAÇÃO DE OUTRO ITEM
    if ($item <> $row[1]) {

        //SALDO ANTERIOR
        $query_anterior = 'select SALDO from UNI_ESTOQUE where DATA <= "' . $data_inicio . '" AND ID_ITEM = ' . $row[1] . ' order by ID DESC LIMIT 1';
        $resultset_anterior = mysql_query($query_anterior);
        $row_anterior = mysql_fetch_array($resultset_anterior);

        $quant_anterior = $row_anterior[0];
        if ($row[3] == 'E') {
            $saldo_correto = $quant_anterior + $row[2];
        } else {
            $saldo_correto = $quant_anterior - $row[2];
        }

        echo '<br><b>NOVO ITEM - ' . $row[1] . '</b><br>';
        echo 'Saldo Anterior: ' . $quant_anterior . '<br>';
        echo $row[0] . ' - ' . $row[1] . ' - ' . $row[2] . ' - ' . $row[3] . ' - ' . $row[4] . ' - ' . $row[5] . '<br>';
        if ($saldo_correto <> $row[4]) { //COMO É A PRIMEIRA MOVIMENTAÇÃO, VERIFICA SE ELA ESTÁ CORRETA, PEGANDO A QUANTIDADE DE ENTRADA E COMPARANDO COM O SALDO  
            echo '<b>PROBLEMA NA MOVIMENTACAO!</b> Saldo correto seria: ' . $saldo_correto . '<br>';
            $query_correcao = 'UPDATE UNI_ESTOQUE set SALDO = ' . $saldo_correto . ' where ID = ' . $row[0]; //REALIZA A CORREÇÃO DO SALDO
            $resultset_correcao = mysql_query($query_correcao);
            //SE ENCONTRAR PROBLEMAS NA PRIMEIRA MOVIMENTAÇÃO DO ITEM, PEGA O SALDO CORRETO E A QUANTIDADE PARA COMPARATIVO BASEADO NA QUANTIDADE MOVIMENTADA
            $quant_anterior = $saldo_correto;
        } else {
            //SE NÃO ENCONTRAR PROBLEMAS NA PRIMEIRA MOVIMENTAÇÃO DO ITEM, PEGA O SALDO CORRETO E A QUANTIDADE PARA COMPARATIVO BASEADO NO SALDO ATUAL
            $saldo_correto = $row[4];
            $quant_anterior = $row[4];
        }
    } else {
        echo $row[0] . ' - ' . $row[1] . ' - ' . $row[2] . ' - ' . $row[3] . ' - ' . $row[4] . ' - ' . $row[5] . '<br>';
        //FAZ O CALCULO DO SALDO CORRETO, VERIFICANDO O TIPO DE OPERAÇÃO (ENTRADA OU SAIDA)
        if ($row[3] == 'E') {
            $saldo_correto = $quant_anterior + $row[2];
        } else {
            $saldo_correto = $quant_anterior - $row[2];
        }
        //VERIFICA DIVERGÊNCIA NO SALDO ATUAL COM O SALDO CORRETO
        if (number_format($saldo_correto, 4, '.', '') <> $row[4]) {
            $erro = true; //COMO ENCONTROU ERRO NA MOVIMENTAÇÃO, MARCA VARIÁVEL ERRO COMO TRUE. ELA SERÁ UTILIZADA PARA VERIFICAÇÕES DE SALDO ANTERIOR
            echo '<b>PROBLEMA NA MOVIMENTACAO!</b> Saldo correto seria: ' . $saldo_correto . '<br>';
            //REALIZA A CORREÇÃO DO SALDO
            $query_correcao = 'UPDATE UNI_ESTOQUE set SALDO = ' . number_format($saldo_correto, 4, '.', '') . ' where ID = ' . $row[0];
            $resultset_correcao = mysql_query($query_correcao);
        }

        if ($erro == false) {
            //SE IDENTIFICOU QUE NÃO HOUVE ERRO NA MOVIMENTAÇÃO, PEGA O SALDO ATUAL PARA VERIFICAÇÃO NA PRÓXIMA MOVIMENTAÇÃO
            $quant_anterior = $row[4];
        } else {
            //SE IDENTIFICOU QUE HOUVE ERRO NA MOVIMENTAÇÃO, PEGA O SALDO CORRETO PARA VERIFICAÇÃO NA PRÓXIMA MOVIMENTAÇÃO
            $quant_anterior = $saldo_correto;
        }
    }

    $item = $row[1]; //PEGA O ID DO ÚLTIMO ITEM A SER VERIFICADO, ASSIM PODEMOS IDENTIFICAR QUANDO TERMINA A VERIFICAÇÃO DE UM ITEM E COMEÇA A VERIFICAÇÃO DE OUTRO (JÁ QUE A PRIMEIRA MOVIMENTAÇÃO DE UM ITEM POSSUI VERIFICAÇÕES DIFERENCIADAS)

    $erro = false; //COMO TERMINOU A VERIFICAÇÃO DESTA MOVIMENTAÇÃO, SETA FALSE NO ERRO PARA COMEÇAR A VERIFICAÇÃO DA PRÓXIMA MOVIMENTAÇÃO
}


$conexao->desconecta();
