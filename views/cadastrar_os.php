<?php 
  include "../config/db.php";
  include "../classes/header.php";
  include "../classes/funcoes.php";
  include "../config/db_oracle.php";

  $periodo = filter_input(INPUT_GET,'periodo');
  $data = filter_input(INPUT_GET,'calendario');
  $dataInicial = str_replace("start=",'',$data);
  $dataFinalBruto = filter_input(INPUT_GET,'end');
  list($anoI,$mesI,$diaI) = explode('-',$dataInicial);
  list($anoF,$mesF,$diaF) = explode('-',$dataFinalBruto);
  
  $dataInicial = "$diaI/$mesI/$anoI";
  $dataFinal = "$diaF/$mesF/$anoF";
  $dataInicial = converteData($dataInicial);
  $dataFinal = converteData($dataFinal);
  
 
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

    $sql_os ="select * from cplus.tva1700 WHERE DTAGEN IS NOT NULL AND DTEXEC IS NULL AND PERIODO = $periodo AND DTAGEN BETWEEN '$dataInicial' AND '$dataFinal' AND codser LIKE '3%' AND OBSER1 NOT LIKE '%CORTE%' AND status = 'A' ";
    $listaOS = oci_parse($conn,$sql_os);
    oci_execute($listaOS);

    // OSs Inseridas no SIStema
    $sql_os_sistema = "SELECT numero_os,tecnico FROM os WHERE os_concluida = 0";
    $ordemInseridaNoSistema = mysqli_query($conectar,$sql_os_sistema);
    
    $osIncluida = array(); 
    
    while($resultado = mysqli_fetch_array($ordemInseridaNoSistema))
    {
      array_push($osIncluida,"$resultado[numero_os]");      
    }
  ?>

  <div id="main" class="container-fluid">
    <h3 class="page-header">Designar Ordem de Serviço</h3>
  </div>

  <form action="../classes/designador.php" method="post" enctype="multipart/form-data" novalidate="novalidate" accept-charset="UTF-8">
    <input type=hidden name=dataInicial value=<?php echo $data; ?>></input>
    <input type=hidden name=dataFinal value=<?php echo $dataFinalBruto; ?>></input>

    <div class="row">
      <div class="form-group col-md-12">
        <div class="form-group col-md-2">
          <!-- EXIBE TECNICOS -->
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
      
      <!-- EXIBE ORDENS -->
        <!-- CAIXA DA ESQUERDA-->
        <div class="dual-list list-left col-md-4">
          <div class="well text-right">
            <div class="row">
              <div class="col-md-10">
                <div class="input-group">
                  <span class="input-group-addon glyphicon glyphicon-search"></span>
                  <input type="text" name="SearchDualList" class="form-control" placeholder="search" />
                </div>
              </div>
              <div class="col-md-2">
                <div class="btn-group">
                  <a class="btn btn-default selector" title="select all"><i class="glyphicon glyphicon-unchecked"></i></a>
                </div>
              </div>
            </div>
            <ul class="list-group ordensservicolista">
              <?php 
                  $qtdOS = 0;
                  while( $row = oci_fetch_array($listaOS,OCI_BOTH)) 
                  { 
                    if(in_array($row['OS'],$osIncluida,true) == false)
                    {
                      $qtdOS++;
                        
                      echo "
                        <li class='list-group-item' value=$row[OS]><input id=listaOSEscondido type=hidden  name='' value='$row[OS]'>$row[OS]</li>
                      ";
                    }
                  }
              ?>  
            </ul>

            <div class=panel-footer>
              <label for="quantidadeOSTotalDiaria">Quantidade Total</label>
              <span id="quantidadeOSTotalDiaria" class="badge badge-pill badge-danger" value=<?php echo $qtdOS;?>><?php echo $qtdOS; ?></span>
            </div>

          </div>
        </div>
        <!-- FIM CAIXA DA ESQUERDA-->

        <!-- SETAS DE TRANSFERENCIA -->
        <div class="list-arrows col-md-1 text-center">
          <button type=button class="btn btn-default btn-sm move-left">
            <span class="glyphicon glyphicon-chevron-left"></span>
          </button>

          <button type=button class="btn btn-default btn-sm move-right">
            <span class="glyphicon glyphicon-chevron-right"></span>
          </button>
        </div>
        <!-- FIM SETAS DE TRANSFERENCIA -->
        
        <!-- CAIXA DA DIREITA-->
        <div class="dual-list list-right col-md-4">
          <div class="well">
            <div class="row">
              <div class="col-md-2">
                <div class="btn-group">
                  <a class="btn btn-default selector" title="select all"><i class="glyphicon glyphicon-unchecked"></i></a>
                </div>
              </div>
              <div class="col-md-10">
                <div class="input-group">
                  <input type="text" name="SearchDualList" class="form-control" placeholder="search" />
                  <span class="input-group-addon glyphicon glyphicon-search"></span>
                </div>
              </div>
            </div>
            
            <ul class="list-group ordensservicolista">
              
            </ul>

            <div class=panel-footer>
              <label for="quantidadeOSDiaria">Quantidade:</label>
              <span id="quantidadeOSDiaria" class="badge badge-pill badge-danger" value=0 readonly>0</span>
            </div>
            
          </div>
        </div><!-- FIM CAIXA DA DIREITA -->
      </div>  <!-- fim colmd12 -->
    </div> <!-- fim row -->
    
    <div id="actions" class="row">
      <div class='col-md-3 col-md-offset-3'>
        <button type="submit" class="btn btn-primary col-md-12">Designar</button>
      </div>
      <div class='col-md-3'>
        <a href="os_ativa.php" class="btn btn-default col-md-12">Cancelar</a>
      </div>
    </div>
  </form> 
  
</body>

<?php include "../classes/footer.php";?>
