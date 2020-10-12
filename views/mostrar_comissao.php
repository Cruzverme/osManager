<?php 
  include "../config/db.php";
  include "../classes/header.php";
  include "../config/db_oracle.php";
  include "../classes/funcoes.php";

  $equipe = filter_input(INPUT_POST,"equipe");
  $tipo = filter_input(INPUT_POST,"tipoRelatorio");
  $dataInicialSelected = filter_input(INPUT_POST,"start");
  $dataFinalSelected = filter_input(INPUT_POST,"end");
error_reporting(0);
  $desabilitar = "";
  switch($tipo)
  {
    case 'assistencia': $labelTipo = 'Assistência';break;
    case 'instalacao': $labelTipo = 'Instalação';break;
    case 'desconexao': $labelTipo = 'Desconexão';break;

  }

  if (!$permiteEditarOS AND $permissao != 99) {
      $desabilitar = "disabled";
  }

  $listaComissao = getOsDetails($equipe, $dataInicialSelected, $dataFinalSelected, $tipo);
?>

<body>
  <div class=container-fluid>
    <div class=row>
      <div class='col-md-2 col-md-offset-5'>
        <figure>
          <img src="../assets/images/logo.jpg" alt="Logo Vertv">
        </figure>
      </div>
    </div>
    <div class=row>
      <div class='col-md-12'>
        <?php echo "<center><h1>Comissão de $labelTipo da $equipe entre $dataInicialSelected - $dataFinalSelected </h1></center>";?>
      </div>
    </div>
    <div class=row>
     <form action="pdf.php" target="_blank" method="POST">
        <div class='col-md-12'>
          <button type='submit' class='btn btn-info pull-right'> <span class='glyphicon glyphicon-save-file'>  GERAR PDF</span></button>
        <div>
        <input type="hidden" name="listaComissao" value="<?php echo htmlspecialchars(serialize($listaComissao))?>">
        <input type="hidden" name="tipoRelatorio" value="<?php echo $tipo ?>" />
        <input type="hidden" name="equipe" value="<?php echo $equipe ?>" />
        <input type="hidden" name="start" value="<?php echo $dataInicialSelected ?>" />
        <input type="hidden" name="end" value="<?php echo $dataFinalSelected ?>" />
     </form>
    </div>
    
    <div class="table-responsive col-md-12">
      <table class="table table-striped table-hover display" id='tabelaComissao' cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th>Serviço</th>
            <th>Dia do Agendamento</th>
            <th>Dia da Execução</th>
            <th>Numero da OS</th>
            <th>Contrato Do Cliente</th>
            <th>Valor da OS(R$)</th>
            <th>PP</th>
            <th>ADICIO</th>
            <th>APTO</th>
            <th class='action'>Ações</th>
          </tr>
        </thead>
        
        <tbody>
          <?php
            $soma = 0.00;
            $quantidade_OS = 0;
            $quantidade_obs = 0;
            $listaObservacoes = array();
            foreach ($listaComissao as $comissao) {
                $linhaComissao = $comissao['valorComissao'];
                if ($comissao['obsEdited']) {
                    $quantidade_obs+=1;
                    $linhaComissao = "$comissao[valorComissao]<sup style='font-size: 9px'><a id='linha$quantidade_obs' href='#ref$quantidade_obs'>$quantidade_obs</a></sup>";
                    array_push($listaObservacoes, "<p id='ref$quantidade_obs'> <sup><a href='#linha$quantidade_obs'>$quantidade_obs</a></sup>$comissao[obsEdited] </p>");
                }
              echo "<tr>
                      <td>$comissao[nomeServico]</td>
                      <td>$comissao[dataAgendamento]</td>
                      <td>$comissao[dataExecucao]</td>
                      <td>$comissao[numeroOS]</td>
                      <td>$comissao[numeroContrato]</td>
                      <td>$linhaComissao</td>
                      <td>$comissao[qtdPontoPrincipal]</td>
                      <td>$comissao[qtdPontoSecundario]</td>
                      <td>$comissao[numeroApto]</td>
                      <td>
                          <button class='btn btn-default' type='button' onClick = 'ajustarValorComissao($comissao[numeroOS], $comissao[numeroContrato])' $desabilitar> 
                            <span class='glyphicon glyphicon-cog'></span>
                          </button>
                      </td>
                    </tr>";

                    $quantidade_OS+=1;
                    $valor_comissao = str_replace(',','.',$comissao['valorComissao']);
                    $soma+=$valor_comissao;
            }//FIM FOREACH
            echo "<p style='font-size:30px;'>Valor a ser pago: R$".str_replace('.',',',$soma)." | Total de OS: ".$quantidade_OS."</p>";
          ?>
        </tbody>
      </table>
        <div class="observations">
            <?php
                foreach ($listaObservacoes as $observacoes) {
                    echo $observacoes;
                }
            ?>
        </div>
    </div>

  </div>
</body>

<!-- MODAL -->
    <div class="form-content" style="display:none;">
      <form class="form" role="form">
        <div class="form-group">
          <label for="campoValorComissao">Valor da Comissão</label>
          <input type="number" min="0.00" step=any id="campoValorComissao" name="valor_comissao" placeholder="Insira a comissão" class="form-control">
          <label for="editOSObservation">Digite o motivo da edição</label>
          <input type="text" id="editOSObservation" class="form-control" name="obsEditOSValue">
        </div>
      </form>
    </div>

<?php include "../classes/footer.php";?>
