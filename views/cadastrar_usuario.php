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
    <h3 class="page-header">Cadastro de Usuário</h3>
  </div>

  <form action="../classesUsuario/novo_usuario.php" method="post">
    <div class="row">
      <div class="col-md-2"></div>

      <div class="form-group col-md-4">
        <label for="campo1">Nome Usuário</label>
        <input type="text" name="nomeUsuario" class="form-control" placeholder="Digite o nome do Usuário" id="campo1">
      </div>
      
      <div class="form-group col-md-4">
        <label for="campo2">Usuário</label>
        <input type="text" name="user" class="form-control"  placeholder="Digite o usuario" id="campo3">
      </div>
    </div>
    
    <div class="row">
      <div class="col-md-2"></div>
      
      <div class="form-group col-md-4">
        <label for="campo3">Senha</label>
        <input type="password" class="form-control" name=senha placeholder="Digite a senha" id="campo3">
      </div>

      <div class="form-group col-md-4">
        <label for="campo1">Selecione o Tipo de Usuário</label>
        <select name=permissao id="campo1" class='form-control'>
          <option value=0>Usuário</option>
          <option value=99>Administrador</option>
        </select>
      </div>    
    </div>
    
    <hr />
    
    <div id="actions" class="row">
      <div class="col-md-12">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="gerenciar_usuarios.php" class="btn btn-default">Voltar</a>
      </div>
    </div>
  </form>
</body>

<?php include "../classes/footer.php";?>