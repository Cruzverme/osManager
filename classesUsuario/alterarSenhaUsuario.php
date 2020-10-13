<?php 
  include "../config/db.php";
  include "../classes/verifica_sessao.php";

  $id_usuario = filter_input(INPUT_POST,"id_usuario");
  $parametros = filter_input(INPUT_POST,"parametros");

  $senha = explode('password=',$parametros);
  $permissaoEditarOSBool = 0;
  if($permissao == 99)
  {
    list($separarPermissao,$editComission,$separarSenha) = explode('&',$parametros);
    $senha = explode('password=',$separarSenha);
    $permissao_usuario = explode('permissao=',$separarPermissao);
    $permissaoEditarOS = explode('editComission=', $editComission);
  }

  if ($permissaoEditarOS[1] == 'Sim') {
      $permissaoEditarOSBool = 1;
  }

  $_SESSION["editOS"] = $permissaoEditarOSBool;

  if($senha && $id_usuario)
  {
    if($senha[1] != '')
    {
      if(!isset($permissao_usuario))
      {
        $novaSenha = md5($senha[1]);
        $sql_altera_senha = "UPDATE system_user SET password = '$novaSenha'
          WHERE id = $id_usuario";
      }else{
        $novaSenha = md5($senha[1]);
        $sql_altera_senha = "UPDATE system_user SET password = '$novaSenha',nivel_usuario=$permissao_usuario[1], editOS = $permissaoEditarOSBool
          WHERE id = $id_usuario";
      }
    } else {
      $sql_altera_senha = "UPDATE system_user SET nivel_usuario=$permissao_usuario[1], editOS=$permissaoEditarOSBool
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