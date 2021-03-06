<?php 
  include "../config/db.php";
  include "../classes/header.php";

  function ajustar_array($posicao,$valor,$tec)
  {
    if( $valor > $posicao['primeiro']['numero_os'])
    {
      $posicao['primeiro']['numero_os'] = $valor;
      $posicao['primeiro']['tecnico'] = $tec;
    }elseif($valor == $posicao['primeiro']){
      echo "1";
      echo verifica_igualdade($tec);
      
    }elseif($valor > $posicao['segundo']['numero_os']  ){
      $posicao['segundo']['numero_os'] = $valor;
      $posicao['segundo']['tecnico'] = $tec;
    }elseif($valor == $posicao['segundo']  ){
      echo "2";
      echo verifica_igualdade($tec);
    }elseif($valor > $posicao['terceiro']['numero_os']){
      $posicao['terceiro']['numero_os'] = $valor;
      $posicao['terceiro']['tecnico'] = $tec;
    }elseif($valor == $posicao['terceiro']){
      echo "3";
      echo verifica_igualdade($tec);
    }elseif($valor > $posicao['quarto']['numero_os']){
      $posicao['quarto']['numero_os'] = $valor;
      $posicao['quarto']['tecnico']= $tec;
    }elseif($valor == $posicao['quarto']){
      echo "4";
      echo verifica_igualdade($tec);
    }elseif($valor > $posicao['quinto']['numero_os']){
      $posicao['quinto']['numero_os'] = $valor;
      $posicao['quinto']['tecnico'] = $tec;
    }elseif($valor == $posicao['quinto']){
      echo "5";
      echo verifica_igualdade($tec);
    }
    return $posicao;
  }

  $sqlTec = "select nome FROM users";
  $execNome = mysqli_query($conectar,$sqlTec);

  $classificacao = 0;
  $osRealizada['os'] = array();
?>

<body>
  <?php 
    include "../classes/nav.php";
  ?>
  <div class=container>
    
    <div class="row justify-content-end">
      <div class="col-md-12" align=center>
        <h1>BEM VINDO</h1>
      </div>
    </div>
    <hr>
    
    <div class="row justify-content-start">
      <div class="panel-group">
        <div class='col-md-4'>
          <div class="panel panel-warning">
            <div class="panel-heading"> OS Pendente de Execução </div>
            <div class="panel-body">
              <ul class=list-group>
                <form role="form" method="post"></form>
                <p>
                  <select id="seletor_tecnico_os" class='form-control' name="nome_tecnico">
                    <option>Escolha o Técnico</option>
                    <?php 
                      while($rowTec = mysqli_fetch_array($execNome,MYSQLI_NUM))
                      {
                        
                        echo "<option value=$rowTec[0]>$rowTec[0]</option>";
                      }
                    ?>
                  </select>
                </p>
                <div>
                  <ul class="list-group">
                    
                  </ul>
                </div>
              </ul>
            </div>
          </div>
        </div>

        <div class='col-md-4'>
          <div class="panel panel-success">
            <div class="panel-heading">Quantidade de OS</div>
            <div class="panel-body">
              <ul class=list-group>
                <h4>
                  Tecnico  <span class='badge badge-pill badge-success'>Realizadas</span> 
                  <span class='badge badge-pill badge-danger'>Canceladas</span>
                  <span class='badge badge-pill badge-dark'>Total</span>
                </h4>
                <?php
                  $sql= " SELECT COUNT(o.numero_os),u.nome FROM os o 
                            INNER JOIN users u ON u.nome = o.tecnico
                            WHERE u.nome = o.tecnico
                            GROUP BY o.tecnico,u.nome
                        ";
                  $execQuery = mysqli_query($conectar,$sql);
                
                  while($totalOS = mysqli_fetch_array($execQuery,MYSQLI_NUM))
                  {
                    $sqlExecutada= "SELECT COUNT(o.ordemServico),u.nome FROM ordensServicos o 
                            INNER JOIN users u ON u.nome = '$totalOS[1]'
                            WHERE u.nome = o.tecnico AND o.status = 1
                        ";
                    $execQueryExecutadas = mysqli_query($conectar,$sqlExecutada);
                    
                    while($osRealizada = mysqli_fetch_array($execQueryExecutadas,MYSQLI_BOTH))
                    {
                      $sqlCanceladas= "SELECT COUNT(o.ordemServico),u.nome FROM ordensServicos o 
                            INNER JOIN users u ON u.nome = '$totalOS[1]'
                            WHERE u.nome = o.tecnico AND o.status = 2
                        ";
                      $execQueryCanceladas = mysqli_query($conectar,$sqlCanceladas);
                      
                      while($osCancelada = mysqli_fetch_array($execQueryCanceladas,MYSQLI_BOTH))
                      {
                        echo "  <li class='list-group-item d-flex justify-content-between align-items-center'>
                                  <span style='padding-left: 12px';>$totalOS[1] </span>
                                  <span class='badge badge-pill badge-dark' > $totalOS[0]</span>
                                  <span class='badge badge-pill badge-danger'> $osCancelada[0]</span>
                                  <span class='badge badge-pill badge-success'> $osRealizada[0]</span>
                                </li>";
                      }
                    }
                  }
                ?>
              </ul>
            </div>
          </div>
        </div>

        <div class='col-md-4'>
          <div class="panel panel-info">
            <div class="panel-heading">Ultimas Ordens</div>
            <div class="panel-body">
              <ul class='list-group'>
                <?php 
                  $sqlOrdens = "SELECT ordemServico,tecnico,status,diaExecutado FROM ordensServicos  
                                ORDER BY diaExecutado DESC LIMIT 10";

                  $execOrdens = mysqli_query($conectar,$sqlOrdens);

                  while($ordensServ = mysqli_fetch_array($execOrdens,MYSQLI_NUM))
                  {
                    echo "<li class='list-group-item'>
                      <strong>OS:</strong> $ordensServ[0]  <strong>Técnico:</strong> $ordensServ[1] <strong>Data:</strong> $ordensServ[3] ";
                      if($ordensServ[2] == 0) 
                        echo "<span class='badge badge-pill badge-dark' > PENDENTE</span>";
                      elseif($ordensServ[2] == 1)
                        echo "<span class='badge badge-pill badge-success' > CONCLUIDA</span>";
                      else
                        echo "<span class='badge badge-pill badge-danger' > CANCELADA</span>";
                       
                    
                    echo"</li>";
                  }

                  
                
                ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

<?php include "../classes/footer.php";?>