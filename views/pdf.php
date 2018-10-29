<?php 

  require '../lib/vendor/autoload.php';
  include "../classes/funcoes.php";
  include "../config/db_oracle.php";

  $equipe = filter_input(INPUT_GET,"equipe");
  $tipo = filter_input(INPUT_GET,"tipoRelatorio");
  $dataInicial = filter_input(INPUT_GET,"start");
  $dataFinal = filter_input(INPUT_GET,"end");

  try {
    $mpdf = new \Mpdf\Mpdf();
    $stylesheet = file_get_contents('../assets/css/pdf.css');
    $mpdf->WriteHTML($stylesheet,1);
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
            <center><h1>Comissão de $tipo da $equipe entre $dataInicial - $dataFinal </h1></center>
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
    ");

    $dataInicial = converteData($dataInicial);
    $dataFinal = converteData($dataFinal);

    if($tipo == "assistencia")
    {
      $sql_comissao = oci_parse($conn, "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA, d.apto
                  FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c, cplus.tva0900 d WHERE a.contra = d.contra AND
                  b.nome = '$equipe' AND a.codequ = b.codequ AND a.codsere is not null  AND 
                  a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal'  AND a.codser = c.codser AND c.codser LIKE '3%' 
                  ORDER BY a.dtexec ASC");
    }elseif($tipo == "instalacao"){
      $sql_comissao = oci_parse($conn, "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA, d.apto
                  FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c, cplus.tva0900 d WHERE a.contra = d.contra AND
                  b.nome = '$equipe' AND a.codequ = b.codequ AND a.codsere is not null  AND 
                  a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal'  AND a.codser = c.codser AND c.codser NOT LIKE '3%' 
                  ORDER BY a.dtexec ASC");
    }else{
      $sql_comissao = oci_parse($conn, "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA, d.apto
                  FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c, cplus.tva0900 d WHERE a.contra = d.contra AND
                  b.nome = '$equipe' AND a.codequ = b.codequ AND a.codsere is not null  AND 
                  a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal'  AND a.codser = c.codser
                  ORDER BY a.dtexec ASC");
    }
    $ok = oci_execute($sql_comissao);
    
    $soma = 0.00;
    $quantidade_OS = 0;

    while ($resultado = oci_fetch_array($sql_comissao, OCI_BOTH))
    {
      $desativado = "";
      $clienteFibra = verificaPacote($resultado[5],$dataInicial,$dataFinal,$resultado[3]);
      $pontosDoCliente = verificarPontos($resultado[5],$resultado[4]);
      if(sizeOf($clienteFibra) >= 1)
      {
        $resultado[0] = "$resultado[0]-FTTH";
      }else{
        $resultado[0] = "$resultado[0]-HFC";
      }
      if($tipo != "Assistência" and $resultado[9] == null ) //SEM APARTAMENTO
      {
        if(strpos($resultado[0],"CONEXAO PONTO ADICIONAL") !== FALSE)
        {
          if($resultado[7] > 1 AND $resultado[8] >=0)
          {
            $resultado[7] = $resultado[7] - 1;
            $resultado[8] = $resultado[8] + $resultado[7];
            if($resultado[7] !=1)
            {
              $resultado[7] = 1;
            }
            if(sizeOf($clienteFibra) >= 1)
            {
              $resultado[6] = 30.00 + ($resultado[8] * 20.00);//se for fibra o valor e esse
              $desativado = "disabled";
            }else{
              $resultado[6] = 26.56 + ($resultado[8] * 18.00);//se for hfc
              $desativado = "disabled";
            }
          }elseif($resultado[7] == 1 AND $resultado[8] >=0)
          {
            if(sizeOf($clienteFibra) >= 1)
            {
              $resultado[6] = 30.00 + ($resultado[8] * 20.00);//se for fibra o valor e esse
              $desativado = "disabled";
            }
            else
            {
              $resultado[6] = 26.56 + ($resultado[8] * 18.00);
              $desativado = "disabled";
            }
          }elseif($resultado[7] < 1 AND $resultado[8] >=1) //;se for somente para instalar o ponto adicional.
          {
            if(sizeOf($clienteFibra) >= 1)
            {
              $resultado[6] = 30.00 + (($resultado[8] - 1) * 20.00);//diminui 1 porque 1 ponto tem o valor completo
              $desativado = "disabled";
            }
            else
            {
              $resultado[6] = 26.56 + (($resultado[8] - 1) * 18.00);
              $desativado = "disabled";
            }
          }
        }elseif(strpos($resultado[0],"DESCONEXAO") !== FALSE )
        {
            $resultado[6] = 25.00;
        }
        elseif(strpos($resultado[0],"TRANSFERENCIA") !== FALSE)
        {
          if($resultado[7] > 1 AND $resultado[8] >= 0)
          {
            $resultado[7] = $resultado[7] - 1;
            $resultado[8] = $resultado[8] + $resultado[7];
            
            if($resultado[7] !=1)
            {
                $resultado[7] = 1;
            }
            if(sizeOf($clienteFibra) >= 1)
            {
              $resultado[6] = 80.00 + ($resultado[8] * 20.00);
              $desativado = "disabled";
            } 
            else
            { 
              $resultado[6] = 65.86 + ($resultado[8] * 18.00);
              $desativado = "disabled";
            }
          }elseif($resultado[7] == 1 AND $resultado[8] >=0)
          { 
            if(sizeOf($clienteFibra) >= 1)
            {
              $resultado[6] = 80.00 + ($resultado[8] * 20.00);
              $desativado = "disabled";
            }
            else
            {
              $resultado[6]= 65.86 + ($resultado[8] * 18.00 );
              $desativado = "disabled";
            }
          }
        }
        elseif(strpos($resultado[0],"RECONEXAO") !== FALSE )
        {
            if($resultado[7] > 1 AND $resultado[8] >= 0)
            {
              $resultado[7] = $resultado[7] - 1;
              $resultado[8] = $resultado[8] + $resultado[7];
              if($resultado[7] !=1)
              {
                  $resultado[7] = 1;
              }
              if(sizeOf($clienteFibra) >= 1)
              {
                $resultado[6] = 80.00 + ($resultado[8] * 20.00);
                $desativado = "disabled";
              }
              else
              {
                $resultado[6] = 65.86 + ($resultado[8] * 18.00);
                $desativado = "disabled";
              }
            }
            elseif($resultado[7] == 1 AND $resultado[8] >=0)
            {
              if(sizeOf($clienteFibra) >= 1)
              {  
                $resultado[6] = 80.00 + ($resultado[8] * 20.00);
                $desativado = "disabled";
              }
              else
              {
                $resultado[6]= 65.86 + ($resultado[8] * 18.00);
                $desativado = "disabled";
              }
            }
        }
        elseif($resultado[7] > 1 AND $resultado[8] >= 0)
        {
          $resultado[7] = $resultado[7] - 1;
          $resultado[8] = $resultado[8] + $resultado[7];
          
          if($resultado[7] != 1)
          {
            $resultado[7] = 1;
          }
          if(sizeOf($clienteFibra) >= 1)
          {
            $resultado[6] = 80.00 + ($resultado[8] * 20.00);
            $desativado = "disabled";
          }
          else
          {
            $resultado[6] = 65.86 + ($resultado[8] * 18);
            $desativado = "disabled";
          }
        }
        elseif($resultado[7] == 1 AND sizeof($clienteFibra) >=1)
        {
          if($resultado[8] >= 0)
          {
            $resultado[6] = 80.00 + ($resultado[8] * 20.00);//se for predio
            $desativado = "disabled";
          }else{
            $resultado[6] = 80.00;
            $desativado = "disabled";
          }
        }
        elseif($resultado[7] == 0 AND sizeOf($clienteFibra)>=1)
        {
          if($pontosDoCliente[0] > 1)
          {
            $resultado[7] = 1;
            $resultado[8] = $pontosDoCliente[0] - $resultado[7];
          }else{
            $resultado[7] = $pontosDoCliente[0];
            $resultado[8] = 0;
          }
          if($resultado[8] > 0)
          {
            $resultado[6] = 80 + ($resultado[8] * 20.00);
            $desativado = "disabled";
          }
          else{
            $resultado[6] = 80;
            $desativado = "disabled";
          }
        }
      }//FIM TIPO ASSISTENCIA
      elseif($tipo != "Assistência" and $resultado[9] != null)
      {
        if(strpos($resultado[0],"CONEXAO PONTO ADICIONAL") !== FALSE)
        {
          if($resultado[7] > 1 AND $resultado[8] >=0)
          {
            $resultado[7] = $resultado[7] - 1;
            $resultado[8] = $resultado[8] + $resultado[7];
            if($resultado[7] !=1)
            {
              $resultado[7] = 1;
            }
            if(sizeOf($clienteFibra) >= 1)
            {
              $resultado[6] = 30.00 + ($resultado[8] * 20.00);
              $desativado = "disabled";
            }
            else
            {
              $resultado[6] = 26.56 + ($resultado[8] * 18.00);
              $desativado = "disabled";
            }
          }elseif($resultado[7] == 1 AND $resultado[8] >=0)
          {
            if(sizeOf($clienteFibra) >= 1)
            {
              $resultado[6] = 30.00 + ($resultado[8] * 20.00);
              $desativado = "disabled";
            }
            else
            {
              $resultado[6] = 26.56 + ($resultado[8] * 18.00);
              $desativado = "disabled";
            }
          }
        }
        elseif(strpos($resultado[0],"DESCONEXAO ") !== FALSE )
        {
          $resultado[6] = 25.00;
          $desativado = "disabled";
        }
        elseif(strpos($resultado[0],"TRANSFERENCIA") !== FALSE)
        {
          if($resultado[7] > 1 AND $resultado[8] >= 0)
          {
            $resultado[7] = $resultado[7] - 1;
            $resultado[8] = $resultado[8] + $resultado[7];
            if($resultado[7] !=1)
            {
                $resultado[7] = 1;
            }
            if(sizeOf($clienteFibra) >= 1)
            {
              $resultado[6] = 55.00 + ($resultado[8] * 20.00);//se for predio
              $desativado = "disabled";
            }
            else
            {
              $resultado[6] = 38.24 + ($resultado[8] * 18.00);
              $desativado = "disabled";
            }
          }
          elseif($resultado[7] == 1 AND $resultado[8] >=0)
          {
            if(sizeOf($clienteFibra) >= 1)
            {
              $resultado[6] = 55.00 + ($resultado[8] * 20.00);//se for predio
              $desativado = "disabled";
            }
            else
            {
              $resultado[6] = 38.24 + ($resultado[8] * 18.00);
              $desativado = "disabled";
            }
          }
        }
        elseif(strpos($resultado[0],"RECONEXAO") !== FALSE )
        {
          if($resultado[7] > 1 AND $resultado[8] >= 0)
          {
            $resultado[7] = $resultado[7] - 1;
            $resultado[8] = $resultado[8] + $resultado[7];
            if($resultado[7] !=1)
            {
                $resultado[7] = 1;
            }
            if(sizeOf($clienteFibra) >= 1)
            {
              $resultado[6] = 55.00 + ($resultado[8] * 20.00);//se for predio
              $desativado = "disabled";
            }
            else
            {
              $resultado[6] = 38.24 + ($resultado[8] * 18.00);
              $desativado = "disabled";
            }
          }
          elseif($resultado[7] == 1 AND $resultado[8] >=0)
          {
            if(sizeOf($clienteFibra) >= 1)
            {
              $resultado[6] = 55.00 + ($resultado[8] * 20.00);//se for predio
              $desativado = "disabled";
            }
            else
            {
              $resultado[6] = 38.24 + ($resultado[8] * 18.00);
              $desativado = "disabled";
            }
          }
        }
        elseif($resultado[7] > 1 AND $resultado[8] >= 0) //se primeira conexao predio
        {
          $resultado[7] = $resultado[7] - 1;
          $resultado[8] = $resultado[8] + $resultado[7];
          if($resultado[7] != 1)
          {
            $resultado[7] = 1;
          }
          if(sizeOf($clienteFibra) >= 1)
          {
            $resultado[6] = 55.00 + ($resultado[8] * 20.00);//se for predio
            $desativado = "disabled";
          }
          else
          {
            $resultado[6] = 38.24 + ($resultado[8] * 18.00);
            $desativado = "disabled";
          }
        }
        elseif($resultado[7] == 1 AND sizeOf($clienteFibra) >=1)
        {
          if($resultado[8] >= 0)
          {
            $resultado[6] = 55.00 + ($resultado[8] * 20.00);//se for predio
            $desativado = "disabled";
          }else{
            $resultado[6] = 55.00;
            $desativado = "disabled";
          }
        }
        elseif($resultado[7] == 0 AND sizeOf($clienteFibra)>=1)//se no Cplus vier zerado
        {
          if($pontosDoCliente[0] > 1)
          {
            $resultado[7] = 1;
            $resultado[8] = $pontosDoCliente[0] - $resultado[7];
          }else{
            $resultado[7] = $pontosDoCliente[0];
            $resultado[8] = 0;
          }
          if($resultado[8] > 0)
          {
            $resultado[6] = 55.00 + ($resultado[8] * 20.00);
            $desativado = "disabled";
          }
          else{
            $resultado[6] = 55.00;
            $desativado = "disabled";
          }
        }
      }//FIM DE OUTROS SEM SER ASSISTENCIA (INSTALACAO)

    $mpdf->WriteHTML("    
            <tbody>
              <tr>
                <td>$resultado[0]</td>
                <td>$resultado[1]</td>
                <td>$resultado[2]</td>
                <td>$resultado[4]</td>
                <td>$resultado[5]</td>
                <td>$resultado[6]</td>
                <td>$resultado[7]</td>
                <td>$resultado[8]</td>
                <td>$resultado[9]</td>
              </tr>
            </tbody>
          </table>
        </div> 
      </div>    
    ",2);

    }//FIM WHILE

    $mpdf->WriteHTML("
      <p>Valor a ser pago: R$".str_replace('.',',',$soma)." | Total de OS: ".$quantidade_OS."</p>
    ");
    
    $mpdf->Output();

  } catch (\Mpdf\MpdfException $e) {
    echo $e->getMessage();
  }
?>