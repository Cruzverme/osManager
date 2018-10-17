<?php 

  include "../config/db.php";
  session_start();

  $nome = filter_input(INPUT_POST,"nomeTec");
  $user = filter_input(INPUT_POST,"user");
  $senha = md5(filter_input(INPUT_POST,"senha"));
  $nivel = filter_input(INPUT_POST,"nivel");
  
  if($nome && $user && $senha)
  {
    $sql_novo_usuario = "INSERT INTO system_user(usuario, nome, password, nivel)
                      VALUES ('$user','$nome','$senha','$nivel')";
    $executa_insercao = mysqli_query($conectar,$sql_novo_usuario);
    
    if(mysqli_affected_rows($conectar) > 0)
    {
      echo $_SESSION['menssagem'] = "Cadastro realizado com sucesso";
      header('Location: ../views/cadastrar_tecnico.php');
      exit;
    }else{
      echo $_SESSION['menssagem'] = "Ocorreu um erro no cadastro";
      header('Location: ../views/cadastrar_tecnico.php');
      exit;
    }
    
  }
  else{
    echo $_SESSION['menssagem'] = "Campo Faltando!";
    header('Location: ../views/cadastrar_tecnico.php');
    exit;
  }

  


?>