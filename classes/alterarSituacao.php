<?php 
  include "../config/db.php";

  session_start();

  $ordemServico = filter_input(INPUT_POST,"os");
  $contrato = filter_input(INPUT_POST,"contrato");
  $tecnico = filter_input(INPUT_POST,"tec");
  $tipoSituacao = filter_input(INPUT_POST,"tipoSituacao");
  

  if($ordemServico && $contrato && $tipoSituacao && $tecnico)
  {
    if($tipoSituacao == "Concluir")
    {
      $update_situacao = mysqli_query($conectar,"UPDATE ordensservicos SET status = 1
        WHERE ordemServico = $ordemServico AND contrato = $contrato");

      $update_situacao_os_app = mysqli_query($conectar,"UPDATE os SET os_concluida = 1
        WHERE numero_os = $ordemServico AND tecnico = '$tecnico' ");

    }else{
      $update_situacao = mysqli_query($conectar,"UPDATE ordensservicos SET status = 2
        WHERE contrato = $contrato AND ordemServico = $ordemServico ");
      
      $update_situacao_os_app = mysqli_query($conectar,"UPDATE os SET os_concluida = 0
        WHERE numero_os = $ordemServico AND tecnico = '$tecnico' ");
    }
    
    if($update_situacao_os_app && $update_situacao)
    {
      echo "OS $ordemServico do Contrato $contrato Alterada";
    }else{
      echo "Ocorreu ao tentar alterar a situação OS $ordemServico";
    }
  }else{
    echo "Campo Faltando!";
  }
?>