<?php 
  include "../config/db.php";
  session_start();
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
  ?>

  <div id="main" class="container-fluid">
    <h3 class="page-header">Designar Ordem de Serviço</h3>
  </div>

  <form action="../classes/designador.php" method="post" enctype="multipart/form-data" novalidate="novalidate" accept-charset="UTF-8">
    <div class="row">
      <div class="form-group col-md-4">
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

        <div class="form-group col-md-4">
          <label for="campo1">Numero de OS</label>
          <textarea class="form-control" rows="5" name='os_diaria' id="campo1"></textarea>
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