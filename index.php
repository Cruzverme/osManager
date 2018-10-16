<!DOCTYPE html>
<html lang="pt-br">
  <?php session_start(); ?>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OS Manager</title>

    <link href="assets/bootstrap3/css/bootstrap.css" rel="stylesheet">
    <link href="assets/bootstrap3/css/style.css" rel="stylesheet">
    <script src="assets/bootstrap3/js/jquery.js"></script>
    <script src="assets/bootstrap3/js/bootstrap.min.js"></script>
    
    <script src="assets/js/bootbox.min.js"></script>
  </head>

  <body>

    <?php  
      #capturar mensagem
      if(isset($_SESSION['menssagem']) && !empty($_SESSION['menssagem']))
      {
          print "<script>bootbox.alert({
                                          message:\"{$_SESSION['menssagem']}\",
                                          size:'small',
                                          backdrop: true
                                        })</script>";
          unset( $_SESSION['menssagem'] );
      }
    ?>
  
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
        </button>
        <a class="navbar-brand" href="#">Gerenciador de Ordem de Serviço</a>
        </div>
      </div>
    </nav>

    <div id="main" class="container-fluid">
      <h3 class="page-header">Tela de Login</h3>
      <div class="row">
        <h2><strong>Realize o Login </strong> </h2><br>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <!-- Start form -->
            <form  method="post" action="classesUsuario/autenticador.php">
              <div class="form-group">
                <label for="exampleInputEmail1">Email</label>
                <input type="email" class="form-control" id="exampleInputEmail1" name="username" aria-describedby="emailHelp" placeholder="Digite seu Email">
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Senha</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Digite sua senha">
              </div>
              <div class="form-check">
                <button class="btn btn-info" type="button" name="showpassword" id="showpassword" value="Show Password">Mostrar Senha</button>
                <button type="submit" class="btn btn-primary">Entrar</button>
              </div>
            </form>
          <!-- End form -->
        </div>
      </div>
    </div>
    
    <?php session_destroy(); ?>
    <!-- <script src="assets/bootstrap3/js/jquery.js"></script>
    <script src="assets/bootstrap3/js/bootstrap.min.js"></script>
    <script src="assets/js/bootbox.min.js"></script> -->
    <script src="assets/js/vertv.js"></script>

  </body>

</html>