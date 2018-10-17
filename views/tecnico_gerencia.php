  <?php 
  include "../config/db.php";
  session_start();
  include "../classes/header.php";
  
  $sql_usuario = "select * from users";
  $executa = mysqli_query($conectar,$sql_usuario);

?>
<body>
  <?php include "../classes/nav.php";?>


  <div id="main" class="container-fluid">
    <h3 class="page-header">Designar Ordem de Serviço</h3>

    <div class="col-md-12">
      <a href="cadastrar_tecnico.php" class="btn btn-primary pull-right h2">Cadastrar Técnico</a>
    </div>
  </div>
  <div class="col-md-3"></div>
  <div class="table-responsive col-md-6">
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
                      <button class='btn btn-default'>
                        <span class='glyphicon glyphicon-cog' aria-hidden='true'></span>
                      </button>
                      <button class='btn btn-default'>
                        <span class='glyphicon glyphicon-trash' aria-hidden='true'></span>
                      </button>
                    </td>
                  </tr>";
          }  
        ?>
      </tbody>
    </table>
  </div>
  <div class="col-md-3"></div>
  
</body>

<?php include "../classes/footer.php";?>