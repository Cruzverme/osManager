<?php 
  include "../config/db.php";
  
  include "../classes/header.php";
  
  $sql_usuario = "select * from equipes";
  $executa = mysqli_query($conectar,$sql_usuario);

?>
<body>
  <?php include "../classes/nav.php";?>


  <div id="main" class="container-fluid">
    <div class="page-header">
    <h3>Gerencia</h3>
      <h5>Esta sessão gerencia as equipes</h5>
    </div>
    

    <div class="col-md-12">
      <a href="terceirizados.php" class="btn btn-primary pull-right h2">Nova Equipe</a>
    </div>
  </div>
  <div class="col-md-3"></div>
  <div class="table-responsive col-md-6">
    <table class="table table-striped table-hover display" id='tabelaOS' cellspacing="0" cellpadding="0">
      <thead>
        <tr>
          <th>Nome</th>
          <th class="actions">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php 
          while ($row = mysqli_fetch_array($executa))
          {
            echo "<tr>
                    <td>$row[nome]</td>
                    <td>
                      <button name='alterarNomeEquipe' class='btn btn-default' onClick=alterarNomeEquipe($row[id]) >
                        <span class='glyphicon glyphicon-cog' aria-hidden='true'></span>
                      </button>
                      <button name='deletarEquipe' class='btn btn-default' onClick=removerEquipe($row[id])>
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

<!-- MODAL -->
    <div class="form-content" style="display:none;">
      <form class="form" role="form">
        <div class="form-group">
          <label for="nomeEquipe">Digite o novo Nome</label>
          <input type="text" class="form-control" id="nomeEquipe" name="equipeNome" placeholder="Digite o novo nome">
        </div>
      </form>  
    </div>

<?php include "../classes/footer.php";?>