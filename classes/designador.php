<?php
   include_once "../config/db.php";
    session_start();
   $response = array();

  if (!mysqli_connect_errno())
  {
    $nome = filter_input(INPUT_POST,"tecnico");
    $os = filter_input(INPUT_POST,"os_diaria");
    
    if( $nome && $os )
    {
      $ordemServico = explode(PHP_EOL,$os);

      foreach($ordemServico as $ordem)
      {
        if($ordem != 0)
        {
            $sql_query=("INSERT INTO os(numero_os,tecnico) VALUES ('$ordem','$nome')");
            $result = mysqli_query($conectar,$sql_query);
        }
      }

      if ($result)
      {
          $response["success"] = 1;
          $response["message"] = "OS Designada!";
          echo $_SESSION['menssagem'] = "$response[message]";
          header('Location: ../views/cadastrar_os.php');
          mysqli_close($conectar);
          exit;
      }else
      {
          $response["success"] = 0;
          $response["message"] = "Nao foi designar!";
          echo $_SESSION['menssagem'] = "$response[message]";
          header('Location: ../views/cadastrar_os.php');
          mysqli_close($conectar);
          exit;
      }
    }
    else
    {
        $response["success"] = 0;
        $response["message"] = "Campo Faltando!";
        echo $_SESSION['menssagem'] = "$response[message]";
        //header('Location: ../views/cadastrar_os.php');
        var_dump($nome);
        var_dump($os);
        mysqli_close($conectar);
        //exit;
    }
  }else{
    $response["success"] = 0;
    $response["message"] = "Nao consegui entrar no servidor";
    echo $_SESSION['menssagem'] = "$response[message]";
    header('Location: ../index.php');
    mysqli_close($conectar);
    exit;
  }
?>
