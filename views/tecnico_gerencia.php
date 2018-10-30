  <?php 
  include "../config/db.php";
  
  include "../classes/header.php";
  
  $sql_usuario = "select * from users";
  $executa = mysqli_query($conectar,$sql_usuario);

?>
<body>
  <?php include "../classes/nav.php";?>


  <div id="main" class="container-fluid">
    <div id="top" class="row"><!-- <div class="page-header"> -->
      <div class="col-md-5">
        <h2>Cadastro de Técnico</h2>
        <h5>Esta sessão cadastra usuários para acessar o aplicativo</h5>
      </div>
      
      <div class="col-md-7">
        <a href="cadastrar_tecnico.php" class="btn btn-primary pull-right h2">Cadastrar Técnico</a>
      </div>
    </div>
    <hr />
  </div>
  
  
  <div class="table-responsive col-md-6 col-md-offset-3">
    <table class="table table-striped table-hover display" id='tabelaOS' cellspacing="0" cellpadding="0">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Equipe</th>
          <th>Usuario</th>
          <th class="actions">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php 
          while ($row = mysqli_fetch_array($executa))
          {
            echo "<tr>
                    <td>$row[nome]</td>
                    <td>$row[equipe]</td>
                    <td>$row[usuario]</td>
                    <td>
                      <button name='alterarSenha' class='btn btn-default' onClick=alterarSenha($row[id]) >
                        <span class='glyphicon glyphicon-cog' aria-hidden='true'></span>
                      </button>
                      <button name='deletar' class='btn btn-default' onClick=removerTecnico($row[id])>
                        <span class='glyphicon glyphicon-trash' aria-hidden='true'></span>
                      </button>
                    </td>
                  </tr>";
          }
        ?>
      </tbody>
    </table>
  </div>
  
</body>

<!-- MODAL -->
    <div class="form-content" style="display:none;">
      <form class="form" role="form">
        <div class="form-group">
          <label for="password">Digite a Nova Senha</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Password">
        </div>
      </form>  
    </div>

<?php include "../classes/footer.php";?>