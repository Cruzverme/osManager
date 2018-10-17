<?php 
  include "../config/db_oracle.php";

  session_start();

  $ordemServico = filter_input(INPUT_POST,"os");
  $valorDaOrdem = filter_input(INPUT_POST,"valor_comissao");

  if($ordemServico && $valorDaOrdem)
  {
    $update_comissao = oci_parse($conn,"UPDATE cplus.tva1700 SET VLCOM ='$valorDaOrdem'
                        WHERE OS ='$ordemServico' ");
    $executado = oci_execute($update_comissao);
    if($executado)
    {
      oci_free_statement($update_comissao);
	    oci_close($conn);
      echo $_SESSION['menssagem'] = "$ordemServico Alterado para $valorDaOrdem";
      header('Location: ../views/gerenciarComissao.php');
      exit;
    }else{
      oci_free_statement($update_comissao);
	    oci_close($conn);
      echo $_SESSION['menssagem'] = "Ocorreu um erro ao tentar alterar o valor da OS $ordemServico";
      header('Location: ../views/gerenciarComissao.php');
      exit;
    }
  }else{
    echo $_SESSION['menssagem'] = "Campo Faltando!";
    header('Location: ../views/gerenciarComissao.php');
    exit;
  }

  




?>