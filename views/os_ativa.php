<?php 
  include "../config/db.php";
  include "../classes/header.php";
  include "../classes/funcoes.php";
  verificaStatusOS();  
?>

  <body>
    <?php  
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
    ?>
  <body>
    <?php include "../classes/nav.php";?>

    <div id="main" class="container-fluid">
      <div id="top" class="row">
        <div class="col-md-3">
          <h2>Ordem Serviço</h2>
        </div>
    
        <div class="col-md-6">
          <div class="  form-group input-group h2">
            <span class="input-group-btn">
              <button class="btn btn-primary form-control" onClick="javascript:location.reload(true);">
                <span class="">ATUALIZAR PAGINA</span>
              </button>
            </span>
          </div>
        </div>
    
        <div class="col-md-3">
            <a href="cadastrar_os.php" class="btn btn-primary pull-right h2">Desginar OS</a>
        </div>
      </div> <!-- /#top -->
      <?php ?>
      <hr />
      <div id="list" class="row">
      <div class="row-check">
        <div class="form-group">
            <label class="control-label">Filtrar Por:</label>
            <div class="radio">
                <input class="customRadioButton" id="all" name="searchRadio" value="" checked="true" type="radio">
                <label for="all">Tudo</label>
            </div>
            <div class="radio">
                <input class="customRadioButton" id="concluido" name="searchRadio" value="Concluido" type="radio">
                <label for="concluido">Somente Concluida</label>
            </div>
            <div class="radio">
                <input class="customRadioButton" id="aguardando" name="searchRadio" value="Aguardando" type="radio">
                <label for="aguardando">Aguardando Validação</label>
            </div>
            <div class="radio">
                <input class="customRadioButton" id="cancelado" name="searchRadio" value="Cancelado" type="radio">
                <label for="cancelado">Cancelado</label>
            </div>
        </div>
    </div>
      <div class="table-responsive col-md-12">
          <table class="table table-striped table-hover display" id='tabelaOS' cellspacing="0" cellpadding="0">
            <thead>
              <tr>
                <th>Ordem Serviço</th>
                <th>Contrato</th>
                <th>Tecnico-Equipe</th>
                <th>Email do Comprovante</th>
                <th>Assinante</th>
                <th>Rubrica</th>
                <th>Imagem do Problema</th>
                <th>Observação</th>
                <th>Anotações Técnicas</th>
                <th>Serviço Executado</th>
                <th>Data Execução</th>
                <th class="actions">Ações</th>
                <th>Situação</th>
              </tr>
            </thead>
            <tbody>
            <?php 
              $sql_ordem_servico = ("SELECT * FROM ordensServicos");// WHERE status=0
              $ordens = mysqli_query($conectar,$sql_ordem_servico);
              while ($row = mysqli_fetch_array($ordens))
              {
                echo "
                <tr>
                  <td>$row[ordemServico]</td>
                  <td>$row[contrato]</td>
                  <td>$row[tecnico]-$row[equipe]</td>
                  <td>$row[celularComprovante]</td>
                  <td>$row[assinante]</td>";
                  if($row['assinatura'] == null)
                  {
                    echo '<td>Não á assinatura</td>';
                  }else{
                    echo' <td><a href="data:image/jpeg;base64,'.base64_encode( $row['assinatura'] ).'" target=blank/> Assinatura </td>';
                  }
                  if($row['imagem'] == null)
                  {
                    echo '<td>Sem Imagem</td>';
                  }else{
                    echo' <td><a href="data:image/jpeg;base64,'.base64_encode( $row['imagem'] ).'" target=blank/> Problema </td>';
                  }
                  echo"
                  <td>$row[observacao]</td>
                  <td>$row[anotacaoTecnico]</td>
                  <td>$row[servicoExecutado]</td>
                  <td>$row[diaExecutado]</td>";
                  
                  if($row['status'] == 0 || $row['status'] == 2 )
                    $situacao = "";
                  else
                    $situacao = "disabled";
                  
                  echo"
                  <td class='actions'>
                    <button class='btn btn-warning btn-xs' onClick=alterarSituacaoOrdem($row[ordemServico],$row[contrato],'$row[tecnico]','Reativar') $situacao>
                      <span class='glyphicon glyphicon-resize-small' aria-hidden='true'>
                      </span>
                    </button>
                    <!--<a class='btn btn-warning btn-xs' href='#' $situacao>
                      <span class='glyphicon glyphicon-minus' aria-hidden='true'>
                      </span>
                    </a>-->
                    <button class='btn btn-danger btn-xs' onClick=alterarSituacaoOrdem($row[ordemServico],$row[contrato],'$row[tecnico]','Cancelar') $situacao>
                      <span class='glyphicon glyphicon-remove' aria-hidden='true'>
                      </span>
                    </button>
                  </td>";

                  if($row['status'] == 0)
                    echo "<td>Aguardando</td>";
                  elseif($row['status'] == 1)
                    echo "<td>Concluido</td>";
                  else
                    echo "<td>Cancelado</td>";
                  echo "
                </tr>   
                ";
              }
            
            ?>  
            </tbody>
          </table>
        </div>
      </div> <!-- /#list -->
        
      <div id="bottom" class="row">
      
      </div> <!-- /#bottom -->
    </div>  <!-- /#main -->
    
  </body>

<?php include "../classes/footer.php";?>
