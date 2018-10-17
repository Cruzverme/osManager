<?php 
  include "../config/db.php";

  session_start();

  $id_usuario = filter_input(INPUT_POST,"id_usuario");
  $novaSenha = md5(filter_input(INPUT_POST,"novaSenha"));

  if($novaSenha && $id_usuario)
  {
    $sql_altera_senha = "UPDATE users SET password = '$novaSenha' WHERE id = $id_usuario";
    $executa_alteracao = mysqli_query($conectar,$sql_altera_senha);

    if(mysqli_affected_rows($conectar) > 0)
    {
      echo "Senha Alterada!";
    }else{
      echo "Ocorreu um erro no cadastro";
    }
  }
  else{
    echo "Campo Faltando!";
  }
  

?>