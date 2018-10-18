<?php 
  include "../config/db.php";
  
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

    $sql_equipes = "SELECT nome FROM equipes";
    $executa_equipes = mysqli_query($conectar,$sql_equipes);
  ?>

  <div id="main" class="container-fluid">
    <h3 class="page-header">Designar Ordem de Serviço</h3>
  </div>

  <form action="../classes/salvarTecnico.php" method="post">
  <div class="row">
    <div class="col-md-2"></div>

    <div class="form-group col-md-4">
      <label for="campo1">Nome Do Técnico</label>
      <input type="text" name="nomeTec" class="form-control" placeholder="Digite o nome do técnico" id="campo1">
    </div>
    
    <div class="form-group col-md-4">
      <label for="campo2">Usuário</label>
      <input type="text" name="user" class="form-control"  placeholder="Digite o usuario de acesso ao aplicativo" id="campo3">
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-2"></div>
    
    <div class="form-group col-md-4">
      <label for="campo3">Senha</label>
      <input type="password" class="form-control" name=senha placeholder="Digite a senha" id="campo3">
    </div>

    <div class="form-group col-md-4">
      <label for="campo1">Selecione a Equipe</label>
      <select name=equipe id="campo1" class='form-control'>
        <?php
          while($row = mysqli_fetch_array($executa_equipes))
          {
            echo "<option value='$row[nome]'>$row[nome]</option>";
          }
        ?>
      </select>
    </div>    
  </div>
  
  <hr />
  
  <div id="actions" class="row">
    <div class="col-md-12">
      <button type="submit" class="btn btn-primary">Salvar</button>
      <a href="tecnico_gerencia.php" class="btn btn-default">Cancelar</a>
    </div>
  </div>
  </form>
  

</body>

<?php include "../classes/footer.php";?>