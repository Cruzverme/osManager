<?php 
  include "../config/db.php";
  include "../classes/header.php";
?>

<body>
  <?php 
  
    include "../classes/nav.php";
    
    $sql_nome = ("SELECT nome,id FROM users");
    $ordens = mysqli_query($conectar,$sql_nome);
  ?>

  <div id="main" class="container-fluid">
    <h3 class="page-header">Designar Ordem de Serviço</h3>
  </div>

  <form action="index.html">
  <div class="row">
    
    <div class="form-group col-md-4">
      <label for="campo1">Nome Do Técnico</label>
      <input type="text" class="form-control" placeholder="Digite o nome do técnico" id="campo1">
    </div>
    
    <div class="form-group col-md-4">
      <label for="campo2">Usuário</label>
      <input type="text" class="form-control" placeholder="Digite o usuario de acesso ao aplicativo" id="campo3">
    </div>
    
    <div class="form-group col-md-4">
      <label for="campo3">Senha</label>
      <input type="password" class="form-control" placeholder="Digite a senha" id="campo3">
    </div>

  </div>
  
  <div class="row">
    <div class="form-group col-md-4">
      <label for=campos4>Selecione a Equipe</label>
      <select class="form-control" name="carlist" form="carform" id=campo4>
        <option value="volvo">Volvo</option>
        <option value="saab">Saab</option>
        <option value="opel">Opel</option>
        <option value="audi">Audi</option>
      </select>
    </div>
  </div>
  
  <hr />
  
  <div id="actions" class="row">
    <div class="col-md-12">
      <button type="submit" class="btn btn-primary">Salvar</button>
      <a href="#" class="btn btn-default">Cancelar</a>
    </div>
  </div>
  </form>
  

</body>

<?php include "../classes/footer.php";?>