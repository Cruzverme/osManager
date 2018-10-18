<?php 
  include "../config/db.php";

  session_start();

  $id_usuario = filter_input(INPUT_POST,"id_usuario");
  $parametros = filter_input(INPUT_POST,"parametros");
  
  list($separarPermissao,$separarSenha) = explode('&',$parametros);
  $senha = explode('password=',$separarSenha);
  $permissao = explode('permissao=',$separarPermissao);
  
  if($senha && $id_usuario)
  {  
    if($senha[1] != '')
    {
      $novaSenha = md5($senha[1]);
      $sql_altera_senha = "UPDATE system_user SET password = '$novaSenha',nivel_usuario=$permissao[1]
       WHERE id = $id_usuario";
    }
    else
    {
      $sql_altera_senha = "UPDATE system_user SET nivel_usuario=$permissao[1]
       WHERE id = $id_usuario";
    }
      
    $executa_alteracao = mysqli_query($conectar,$sql_altera_senha);

    if(mysqli_affected_rows($conectar) > 0)
    {
      echo "Usuário Alterado!";
    }else{
      echo "Ocorreu um erro na alteração";
    }
  }
  else
  {
    echo "Campo Faltando!";
  }
?>