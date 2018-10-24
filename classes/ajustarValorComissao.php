<?php 
  include "../config/db_oracle.php";

  $ordemServico = filter_input(INPUT_POST,"os");
  $valorDaOrdem = filter_input(INPUT_POST,"valor_comissao");
  if($ordemServico && $valorDaOrdem)
  {
    $valor = explode('valor_comissao=',$valorDaOrdem);
    $update_comissao = oci_parse($conn,"UPDATE cplus.tva1700 SET VLCOM ='$valor[1]'
                        WHERE OS ='$ordemServico' ");
    $executado = oci_execute($update_comissao);
    if($executado)
    {
      oci_free_statement($update_comissao);
      oci_close($conn);
      echo "Valor da Ordem de Serviço $ordemServico Alterado para R$$valor[1]";
    }else{
      oci_free_statement($update_comissao);
      oci_close($conn);
      echo "Ocorreu um erro ao tentar alterar o valor da OS $ordemServico";
    }
  }else{
    echo "Campo Faltando!";
  }

?>
