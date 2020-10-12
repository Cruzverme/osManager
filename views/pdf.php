<?php 

  require '../lib/vendor/autoload.php';
  include "../classes/funcoes.php";
  include "../config/db_oracle.php";

  $equipe = filter_input(INPUT_POST,"equipe");
  $tipo = filter_input(INPUT_POST,"tipoRelatorio");
  $dataInicial = filter_input(INPUT_POST,"start");
  $dataFinal = filter_input(INPUT_POST,"end");
  $dataInfo = filter_input(INPUT_POST,'listaComissao');

  $listaComissao = unserialize(stripslashes( htmlspecialchars_decode($dataInfo)));

  switch($tipo)
  {
    case 'assistencia': $labelTipo = 'Assistência';break;
    case 'instalacao': $labelTipo = 'Instalação';break;
    case 'desconexao': $labelTipo = 'Desconexão';break;
  }

  try {
   
    $mpdf = new \Mpdf\Mpdf();
    $stylesheet = file_get_contents('../assets/css/pdf.css');
    $mpdf->WriteHTML($stylesheet,1);
    $mpdf->WriteHTML("<title>Comissão de $equipe </title>",1);
    $mpdf->shrink_tables_to_fit = 1;
    $mpdf->WriteHTML("
     
      <div class=container-fluid>
        <div class=row>
          <div class='col-md-2 col-md-offset-5'>
            <figure>
              <img src='../assets/images/logo.jpg' alt='Logo Vertv'>
            </figure>
          </div>
        </div>
        <div class=row>
          <div class='col-md-12'>
            <center><h1>Comissão de $labelTipo da $equipe entre $dataInicial - $dataFinal </h1></center>
          </div>
        </div>",2);
    
    $mpdf->WriteHTML("    
        <div class='table-responsive col-md-12'>
          <table autosize=1 class='table table-striped table-hover display' id='tabelaComissao' cellspacing='0' cellpadding='0'>
            <thead>
              <tr>
                <th>Serviço</th>
                <th>Dia do Agendamento</th>
                <th>Dia da Execução</th>
                <th>Numero da OS</th>
                <th>Contrato Do Cliente</th>
                <th>Valor da OS(R$)</th>
                <th>PP </th>
                <th>ADICIO</th>
                <th>APTO</th>
              </tr>
            </thead>
            <tbody>
    ");
    $soma = 0.00;
    $quantidade_OS = 0;
    $quantidade_obs = 0;
    $listaObservacoes = array();

    foreach ($listaComissao as $comissao) {
      $linhaComissao = $comissao['valorComissao'];
      if ($comissao['obsEdited']) {
        $quantidade_obs+=1;
        $linhaComissao = "$comissao[valorComissao]<sup style='font-size: 9px'><a id='linha$quantidade_obs' name='linha$quantidade_obs' href='#ref$quantidade_obs'>$quantidade_obs</a></sup>";
        array_push($listaObservacoes, "<p id='ref$quantidade_obs'> <sup><a href='#linha$quantidade_obs' name='ref$quantidade_obs'>$quantidade_obs</a></sup>$comissao[obsEdited] </p>");
      }

      $mpdf->WriteHTML("    
                <tr>
                  <td>$comissao[nomeServico]</td>
                  <td>$comissao[dataAgendamento]</td>
                  <td>$comissao[dataExecucao]</td>
                  <td>$comissao[numeroOS]</td>
                  <td>$comissao[numeroContrato]</td>
                  <td>$linhaComissao</td>
                  <td>$comissao[qtdPontoPrincipal]</td>
                  <td>$comissao[qtdPontoSecundario]</td>
                  <td>$comissao[numeroApto]</td>
                </tr>
      ",2);
        $quantidade_OS+=1;
        $valor_comissao = str_replace(',','.',$comissao['valorComissao']);
        $soma+=$valor_comissao;
    }//FIM WHILE

    
    $mpdf->WriteHTML("
            </tbody>
          </table>
        </div> 
        <p>Valor a ser pago: R$ ".str_replace('.',',',$soma)." | Total de OS: ".$quantidade_OS."</p>
        <div class='observations'>
      
    ",2);

  foreach ($listaObservacoes as $observacoes) {
    $mpdf->WriteHTML("$observacoes", 2);
  }
    $mpdf->WriteHTML("
        </div>
      </div>
    ", 2);
    $mpdf->Output();

  } catch (\Mpdf\MpdfException $e) {
    echo $e->getMessage();
  }
?>
