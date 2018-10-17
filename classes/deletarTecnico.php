<?php 
  include "../config/db.php";

  session_start();

  $id_usuario = filter_input(INPUT_POST,"id_usuario");

  if($id_usuario)
  {
    $sql_deletarTecnico = "DELETE FROM users WHERE id = $id_usuario";
    $executa_alteracao = mysqli_query($conectar,$sql_deletarTecnico);

    if(mysqli_affected_rows($conectar) > 0)
    {
      echo "Usuário Removido!";
    }else{
      echo "Ocorreu um erro no cadastro";
    }
  }
  else{
    echo "Campo Faltando!";
  }
?>