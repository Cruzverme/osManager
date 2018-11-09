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

    $sql_nome = ("SELECT nome,id FROM users");
    $ordens = mysqli_query($conectar,$sql_nome);

    $sql_os = "SELECT ordemServico FROM ordensservicos";
    $listaOS = mysqli_query($conectar,$sql_os);
  ?>

  <div id="main" class="container-fluid">
    <h3 class="page-header">Designar Ordem de Serviço</h3>
  </div>

  <form action="../classes/designador.php" method="post" enctype="multipart/form-data" novalidate="novalidate" accept-charset="UTF-8"> 
    <div class="row">
    <div class="form-group col-md-12">
      <div class="form-group col-md-6">
        <label for="campo2">Técnico</label>
        <select name='tecnico' class="form-control" id=campo2>
        <?php
          if(!$ordens)
          {
            die(mysqli_error());
          }else{
            while( $row = mysqli_fetch_array($ordens))
            {
              echo "<option value=$row[nome]>$row[nome]</option>";
            }
            mysqli_close($conectar);
          }
        ?>
        </select>
        </div>

        <div class="dual-list list-right col-md-6">
          <div class="well">
            <div class="row">
              <div class="col-md-1">
                <div class="btn-group">
                  <a class="btn btn-default selector" title="select all"><i class="glyphicon glyphicon-unchecked"></i></a>
                </div>
              </div>

              <div class="col-md-1">
                <div class="btn-group">
                  <a class="btn btn-default remover" title="remove"><i class="glyphicon glyphicon-remove"></i></a>
                </div>
              </div>

              <div class="col-md-10"> 
                <div class="input-group">
                  <input type="text" name="SearchDualList" class="form-control" placeholder="search"/>
                  <span class="input-group-addon glyphicon glyphicon-search"></span>
                </div>
              </div>
            </div>
            
            <ul class="list-group" >
              
            </ul>

            <div class='row'>
              <div class='col-md-3'>
                <label for="ordemServicoADD">Ordem de Serviço</label>
              </div>
              <div class='col-md-7'>
                <input type='text' class='form-control novaOrdemServico' id='ordemServicoADD' name='osADD' onkeydown="if (event.keyCode == 13) return false"/>
              </div>
              <div class='col-md-2'>
                <div class="btn-group">
                  <a class="btn btn-default add" title="Adicionar Ordem de Serviço"><i class="glyphicon glyphicon-plus"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>  
    </div>
    
    <div id="actions" class="row">
      <div class="col-md-12">
        <button type="submit" class="btn btn-primary">Designar</button>
        <a href="os_ativa.php" class="btn btn-default">Cancelar</a>
      </div>
    </div>
  </form> 
  

</body>

<?php include "../classes/footer.php";?>