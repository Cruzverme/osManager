<?php 
  include "../config/db.php";
  session_start();

  $empresa = filter_input(INPUT_POST,"nomeEquipe");


  if($empresa)
  {
    $sql_equipe = "INSERT INTO equipes(nome) VALUES ('$empresa')";
    $executa = mysqli_query($conectar,$sql_equipe);
    
    if(mysqli_affected_rows($conectar) > 0)
    {
      echo $_SESSION['menssagem'] = "Cadastro realizado com sucesso";
      header('Location: ../views/terceirizados.php');
      exit;
    }else{
      echo $_SESSION['menssagem'] = "Ocorreu um erro no cadastro";
      var_dump($executa);
      header('Location: ../views/terceirizados.php');
      exit;
    } 
  }
  else{
    echo $_SESSION['menssagem'] = "Campo Faltando!";
    header('Location: ../views/terceirizados.php');
    exit;
  }



  
  

?>