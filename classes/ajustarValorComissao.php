<?php
include "../config/db_oracle.php";
include "../config/db.php";
include "../classes/verifica_sessao.php";

$ordemServico = filter_input(INPUT_POST,"os");
$valorDaOrdemComObs = filter_input(INPUT_POST,"valor_comissao");
$contrato = filter_input(INPUT_POST,"contrato");

if ($ordemServico && $valorDaOrdemComObs && $contrato)
{
    list($valorDaOrdem, $obs) = explode('&', $valorDaOrdemComObs);

    $valor = explode('valor_comissao=',$valorDaOrdem);
    $observacao = explode('obsEditOSValue=',$obs);
    $observacao = urldecode($observacao[1]);

    $update_comissao = oci_parse($conn,"UPDATE cplus.tva1700 SET VLCOM ='$valor[1]'
                        WHERE OS ='$ordemServico' ");
    $executado = oci_execute($update_comissao);

    if ($executado) {
        oci_free_statement($update_comissao);
        oci_close($conn);

        $sql_salvar_obs_os = "INSERT INTO os_comissao_detail(numero_os, contrato, user, obs) 
                      VALUES ($ordemServico, $contrato, $user_ativo, '$observacao')";

        $executa_insercao = mysqli_query($conectar, $sql_salvar_obs_os);

        if (mysqli_affected_rows($conectar) > 0) {
            echo "Valor da Ordem de Serviço $ordemServico Alterado para R$$valor[1] com a justificativa de $observacao";
        }
    } else {
        oci_free_statement($update_comissao);
        oci_close($conn);

        echo "Ocorreu um erro ao tentar alterar o valor da OS $ordemServico";
    }
} else {
    echo "Campo Faltando!";
}

?>
