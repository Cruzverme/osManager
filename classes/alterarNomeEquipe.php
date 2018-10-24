<?php 
  header('Content-Type: text/plain');
  include "../config/db.php";

  session_start();

  $id_equipe = filter_input(INPUT_POST,"id_equipe");
  $novoNome = filter_input(INPUT_POST,"novoNome");
  
  if($novoNome && $id_equipe)
  {
    $nomeEquipe = explode('equipeNome=',$novoNome);
    $nome = urldecode($nomeEquipe[1]);
    $sql_altera_nome = "UPDATE equipes SET nome = '$nome' WHERE id = $id_equipe";
    $executa_alteracao = mysqli_query($conectar,$sql_altera_nome);

    if(mysqli_affected_rows($conectar) > 0)
    {
      echo "Nome da Equipe Alterada para $nome!";
    }else{
      echo "Ocorreu um erro na Alteração";
    }
  }
  else{
    echo "Campo Faltando! $novoNome e $id_equipe" ;
  }
?>