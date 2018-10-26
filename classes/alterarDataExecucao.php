<?php 
  include "../config/db_oracle.php";
  include "../classes/funcoes.php";

  session_start();

  $ordemServico = filter_input(INPUT_POST,"os");
  $dataExecucao = filter_input(INPUT_POST,"data");

  if($ordemServico && $dataExecucao)
  {
    $diaExecucao = converteData($dataExecucao);

    $update_data_os = oci_parse($conn,"UPDATE cplus.tva1700 SET DTEXEC ='$diaExecucao' 
                        WHERE OS = $ordemServico ");
    $executa = oci_execute($update_data_os);
    
    if($executa)
    {
      oci_free_statement($update_data_os);
      oci_close($conn);
      echo $_SESSION['menssagem'] = "Data da OS $ordemServico Alterada para $dataExecucao";
      header('Location: ../views/gerenciarComissao.php');
      exit;
    }else{
      oci_free_statement($update_data_os);
      oci_close($conn);
      echo $_SESSION['menssagem'] = "Data da OS $ordemServico Não Alterada";
      header('Location: ../views/gerenciarComissao.php');
      exit;
    }
  }else{
    echo $_SESSION['menssagem'] = "Campo Faltando";
    header('Location: ../views/gerenciarComissao.php');
    exit;
  }
?>