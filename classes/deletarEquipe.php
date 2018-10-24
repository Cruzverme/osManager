<?php 
  include "../config/db.php";

  session_start();

  $id_equipe = filter_input(INPUT_POST,"id_equipe");

  if($id_equipe)
  {
    $sql_deletarEquipe = "DELETE FROM equipes WHERE id = $id_equipe";
    $executa_alteracao = mysqli_query($conectar,$sql_deletarEquipe);

    if(mysqli_affected_rows($conectar) > 0)
    {
      echo "Equipe Removida!";
    }else{
      echo "Ocorreu um erro na remoção";
    }
  }
  else{
    echo "Campo Faltando!";
  }
?>