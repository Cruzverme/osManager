<?php
  include_once "../config/db.php";
  // Inicia sessões 
  session_start();

  if (!mysqli_connect_errno())
  {
    $usuario = filter_input(INPUT_POST,'username');
    $senha = md5(filter_input(INPUT_POST,'password'));
    
    if($usuario && $senha)
    {
      $sql_verifica_login = ("SELECT * FROM system_user WHERE usuario = '$usuario' " );
      $sql_verifica_password = ("SELECT usuario,password FROM system_user WHERE usuario = '$usuario' AND password = '$senha'" );

      $checar_login = mysqli_query($conectar,$sql_verifica_login);
      $checar_password = mysqli_query($conectar,$sql_verifica_password);

      if (mysqli_num_rows($checar_login) == 0)
      {
        echo $_SESSION['menssagem'] = "Usuario inexistente!";
        header('Location: ../index.php');
        mysqli_close($conectar);
        exit;
      }elseif (mysqli_num_rows($checar_password) == 0) {
        echo $_SESSION['menssagem'] = "Senha Incorreta!";
        header('Location: ../index.php');
        mysqli_close($conectar);
        exit;
      }else{
        $dados = @mysqli_fetch_array($checar_login); 
        // TUDO OK! Agora, passa os dados para a sessão e redireciona o usuário
        $_SESSION["id_usuario"]= $dados["id"];
        $_SESSION["nome_usuario"] = $dados["nome"];
        $_SESSION["nivel"] = $dados["nivel_usuario"];
        $_SESSION["editOS"] = $dados["editOS"];
        
        $date = new DateTime("NOW", new DateTimeZone('America/Sao_Paulo')); //ajusta hora de login

        $nome = $_SESSION['nome_usuario'];
        $usuario_id = $_SESSION["id_usuario"];
        $permissao = $_SESSION["nivel"];
        
        $sql = "UPDATE system_user SET data_login = '{$date->format('Y-m-d H:i:s')}' WHERE id = $usuario_id";
        mysqli_query($conectar,$sql);

        header('Location: ../views/');
        mysqli_close($conectar);
        exit;
      }
    }
    else
    {
      echo $_SESSION['menssagem'] = "Campo Faltando!";
      header('Location: ../index.php');
      mysqli_close($conectar);
      exit;
    }
  }else{
    echo $_SESSION['menssagem'] = "Nao consegui entrar no servidor";
    header('Location: ../index.php');
    mysqli_close($conectar);
    exit;
  }

?>