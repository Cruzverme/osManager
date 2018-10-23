<?php 
  include "../config/db.php";
  include "../classes/header.php";
  include "../config/db_oracle.php";
  include "../classes/funcoes.php";

  $equipe = filter_input(INPUT_POST,"equipe");
  $tipo = filter_input(INPUT_POST,"tipoRelatorio");
  $dataInicial = filter_input(INPUT_POST,"start");
  $dataFinal = filter_input(INPUT_POST,"end");
  
  function converteData($data)
  {

    list($dia,$mes,$ano) = explode('/',$data);

    switch($mes)
    {
      case '01': $mes = 'JAN';break;
      case '02': $mes = 'FEB';break;
      case '03': $mes = 'MAR';break;
      case '04': $mes = 'APR';break;
      case '05': $mes = 'MAY';break;
      case '06': $mes = 'JUN';break;
      case '07': $mes = 'JUL';break;
      case '08': $mes = 'AUG';break;
      case '09': $mes = 'SEP';break;
      case '10': $mes = 'OCT';break;
      case '11': $mes = 'NOV';break;
      case '12': $mes = 'DEC';break;
    }
    return "$dia/$mes/$ano";
  }
  
?>

<body>

  <div class=container-fluid>
    
    <div class=row>
      <div class='col-md-12'>
        <?php echo "<center><h1>Comissão de $tipo da $equipe entre $dataInicial - $dataFinal </h1></center>";?>
      </div>
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
          </tr>
        </thead>
        
        <tbody>
          <?php 
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
          print_r(oci_error($sql_comissao));
//            var_dump($sql_comissao);
          
            $soma = 0.00;
            $quantidade_OS = 0;
            while ($resultado = oci_fetch_array($sql_comissao, OCI_BOTH))
            {
              $clienteFibra = verificaPacote($resultado[5],$dataInicial,$dataFinal,$resultado[3]);

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
                      $resultado[6] = 30.00 + ($resultado[8] * 20.00);//se for fibra o valor e esse
                    else
                      $resultado[6] = 26.56 + ($resultado[8] * 18.00);//se for hfc
                  }elseif($resultado[7] == 1 AND $resultado[8] >=0)
                  {
                    if(sizeOf($clienteFibra) >= 1)
                      $resultado[6] = 30.00 + ($resultado[8] * 20.00);//se for fibra o valor e esse
                    else
                      $resultado[6] = 26.56 + ($resultado[8] * 18.00);
                  }elseif($resultado[7] < 1 AND $resultado[8] >=1) //;se for somente para instalar o ponto adicional.
                  {
                    if(sizeOf($clienteFibra) >= 1)
                      $resultado[6] = 30.00 + (($resultado[8] - 1) * 20.00);//diminui 1 porque 1 ponto tem o valor completo
                    else
                      $resultado[6] = 26.56 + (($resultado[8] - 1) * 18.00);
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
                      $resultado[6] = 80.00 + ($resultado[8] * 20.00);
                    else
                      $resultado[6] = 65.86 + ($resultado[8] * 18.00);
                  }elseif($resultado[7] == 1 AND $resultado[8] >=0)
                  { 
                    if(sizeOf($clienteFibra) >= 1)
                      $resultado[6] = 80.00 + ($resultado[8] * 20.00);
                    else
                      $resultado[6]= 65.86 + ($resultado[8] * 18.00 );
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
                        $resultado[6] = 80.00 + ($resultado[8] * 20.00);
                      else
                        $resultado[6] = 65.86 + ($resultado[8] * 18.00);
                    }
                    elseif($resultado[7] == 1 AND $resultado[8] >=0)
                    {
                      if(sizeOf($clienteFibra) >= 1)
                        $resultado[6] = 80.00 + ($resultado[8] * 20.00);
                      else
                        $resultado[6]= 65.86 + ($resultado[8] * 18.00);
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
                    $resultado[6] = 80.00 + ($resultado[8] * 20.00);
                  else
                    $resultado[6] = 65.86 + ($resultado[8] * 18);
                }
                elseif($resultado[7] == 1 AND sizeof($clienteFibra) >=1)
                {
                  if($resultado[8] >= 0)
                  {
                    $resultado[6] = 80.00 + ($resultado[8] * 20.00);//se for predio
                  }else{
                    $resultado[6] = 80.00;
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
                      $resultado[6] = 30.00 + ($resultado[8] * 20.00);
                    else
                      $resultado[6] = 26.56 + ($resultado[8] * 18.00);
                  }elseif($resultado[7] == 1 AND $resultado[8] >=0)
                  {
                    if(sizeOf($clienteFibra) >= 1)
                      $resultado[6] = 30.00 + ($resultado[8] * 20.00);
                    else
                      $resultado[6] = 26.56 + ($resultado[8] * 18.00);
                  }
                }
                elseif(strpos($resultado[0],"DESCONEXAO ") !== FALSE )
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
                      $resultado[6] = 55.00 + ($resultado[8] * 20.00);//se for predio
                    else  
                      $resultado[6] = 38.24 + ($resultado[8] * 18.00);
                  }
                  elseif($resultado[7] == 1 AND $resultado[8] >=0)
                  {
                    if(sizeOf($clienteFibra) >= 1)
                      $resultado[6] = 55.00 + ($resultado[8] * 20.00);//se for predio
                    else
                      $resultado[6]= 38.24 + ($resultado[8] * 18.00 );
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
                      $resultado[6] = 55.00 + ($resultado[8] * 20.00);//se for predio
                    else
                      $resultado[6] = 38.24 + ($resultado[8] * 18.00);
                  }
                  elseif($resultado[7] == 1 AND $resultado[8] >=0)
                  {
                    if(sizeOf($clienteFibra) >= 1)
                      $resultado[6] = 55.00 + ($resultado[8] * 20.00);//se for predio
                    else
                      $resultado[6]= 38.24 + ($resultado[8] * 18.00);
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
                    $resultado[6] = 55.00 + ($resultado[8] * 20.00);//se for predio
                  else
                    $resultado[6] =  38.24 + ($resultado[8] * 18.00);
                }
                elseif($resultado[7] == 1 AND sizeof($clienteFibra) >=1)
                {
                  if($resultado[8] >= 0)
                  {
                    $resultado[6] = 55.00 + ($resultado[8] * 20.00);//se for predio
                  }else{
                    $resultado[6] = 55.00;
                  }
                }
              }//FIM DE OUTROS SEM SER ASSISTENCIA (INSTALACAO)
              echo "<tr>
                      <th>$resultado[0]</th>
                      <th>$resultado[1]</th>
                      <th>$resultado[2]</th>
                      <th>$resultado[4]</th>
                      <th>$resultado[5]</th>
                      <th>$resultado[6]</th>
                      <th>$resultado[7]</th>
                      <th>$resultado[8]</th>
                      <th>$resultado[9]</th>
                    </tr>";

                    $quantidade_OS+=1;
                    $valor_comissao = str_replace(',','.',$resultado[6]);
                    $soma+=$valor_comissao;
            }//FIM WHILE        
            echo "<p style='font-size:30px;'>Valor a ser pago: R$".str_replace('.',',',$soma)." | Total de OS: ".$quantidade_OS."</p>";
            oci_free_statement($sql_comissao);
            oci_close($conn);
          ?>
        </tbody>
      </table>
    </div>
  
  </div>
</body>

<?php include "../classes/footer.php";?>
