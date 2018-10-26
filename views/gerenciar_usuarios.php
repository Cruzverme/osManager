<?php 
  include "../config/db.php";
  
  include "../classes/header.php";
  
  if($permissao == 99)
    $sql_usuario = "select * from system_user";
  else
    $sql_usuario = "select * from system_user where id = $user_ativo";

  $executa = mysqli_query($conectar,$sql_usuario);

?>
<body>
  <?php include "../classes/nav.php";?>


  <div id="main" class="container-fluid">
    <h3 class="page-header">Cadastro de Usuário</h3>

    <div class="col-md-12">
      <a href="cadastrar_usuario.php" class="btn btn-primary pull-right h2">Cadastrar Usuário</a>
    </div>
  </div>
  <div class="col-md-3"></div>
  <div class="table-responsive col-md-6">
    <table class="table table-striped table-hover display" id='tabelaOS' cellspacing="0" cellpadding="0">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Usuario</th>
          <th>Ultimo Login</th>
          <th>Tipo de Usuário</th>
          <th class="actions">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php 
          while ($row = mysqli_fetch_array($executa))
          {
            echo "<tr>
                    <td>$row[nome]</td>
                    <td>$row[usuario]</td>
                    <td>$row[data_login]</td>";
                    if($row['nivel_usuario'] == 99)
                      echo "<td>Administrador</td>";
                    else
                      echo "<td>Usuário</td>";
            echo"
                    <td>
                      <button name='alterarSenha' class='btn btn-default' onClick=alterarSenhaUsuario($row[id]) >
                        <span class='glyphicon glyphicon-cog' aria-hidden='true'></span>
                      </button>";
            if($permissao == 99)
            {
              echo "  <button name='deletar' class='btn btn-default' onClick=removerUsuario($row[id])>
                        <span class='glyphicon glyphicon-trash' aria-hidden='true'></span>
                      </button>";
            }                      
            echo "         
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
        <?php 
          if($permissao == 99)
          {
            echo "<div class='form-group'>
                    <label for='permissao'>Alterar Tipo de Usuário</label>
                    <select class='form-control' name=permissao>
                      <option value=0>Usuário</option>
                      <option value=99>Administrador</option>
                    </select>
                  </div>
                  ";
          }
        ?>
        
        <div class="form-group">
          <label for="password">Digite a Nova Senha</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Password">
        </div>
      </form>  
    </div>

<?php include "../classes/footer.php";?>