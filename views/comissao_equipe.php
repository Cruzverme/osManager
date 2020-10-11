<?php 
  include "../config/db.php";
  include "../classes/header.php";

  $sql_equipes = "SELECT nome FROM equipes";
  $executa_equipes = mysqli_query($conectar,$sql_equipes);

  $desabilitar = "";
  if (!$permiteEditarOS AND $permissao != 99) {
    $desabilitar = "disabled";
  }
?>

<body>
  <?php include "../classes/nav.php";?>
  
    <div id="main" class="container-fluid">
      <div id="top" class="row">
        <div class="col-md-5">
          <h2>Comissão de Equipes</h2>
          <h5>Verifique o Quanto Cada Equipe Receberá</h5>
        </div>
      
        <div class="col-md-7">
          <a href="gerenciarComissao.php" class="btn btn-primary pull-right h2 <?php echo $desabilitar?>">Ajustar Comissão</a>
        </div>
        
      </div>
      <hr />
      <form action="mostrar_comissao.php" method="post" target="_blank">
      <!-- area de campos do form -->
        <div class="row">
          <div class="form-group col-md-6">
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

          <div class="form-group col-md-6">
            <label for="campo2">Selecione o Tipo de Relatorio</label>
            <select name=tipoRelatorio id=campo2 class=form-control>
              <option value=instalacao>Instalação</option>
              <option value=assistencia>Assistencia</option>
              <option value=desconexao>Desconexão</option>
            </select>
          </div>
        </div>
        
        <div class='form-group col-md-12'>  
          <center>
            <label for="periodo"><h4>Selecione o Periodo</h4></label>
          </center>
          
          <div class="form-group input-group input-daterange">  
            <input type="text" id="periodo" class="input-sm form-control" name="start" />
            <span class="input-group-addon">Até</span>
            <input type="text" class="input-sm form-control" name="end" />    
          </div>
        </div>

        <div id="actions" class="row">
          <div class="col-md-12">
            <button type="submit" class="btn btn-primary">Mostrar Comissão</button>
          </div>
        </div>
      </form>
    </div>


  </div>
</body>

<?php include "../classes/footer.php";?>
