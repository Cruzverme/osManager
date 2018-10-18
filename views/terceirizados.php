<?php 
  include "../classes/header.php";
  
?>
<body>
  <?php 
    include "../classes/nav.php";

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

  <div id="main" class="container-fluid">
    <h3 class="page-header">Cadastrar Equipe</h3>
  </div>

  <form action="../classes/salvarEmpresa.php" method="post">
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-4"></div>
        
        <div class="form-group col-md-4">
        
          <label for="campoNome">Nome da Equipe</label>
          <input type="text" id="campoNome" name="nomeEquipe" class="form-control" placeholder="Insira o nome da equipe" />
          <br>
          <div class="form-group col-md-12">
            <input type="submit" class="form-control btn btn-primary" value="Inserir"/>
          </div>

        </div>
        
      </div>
    </div>
  </form>

</body>

<?php include "../classes/footer.php";?>