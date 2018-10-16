<?php 
  include "../config/db.php";
  include "../classes/header.php";
  include "../config/db_oracle.php";

  $equipe = filter_input(INPUT_POST,"equipe");
  $tipo = filter_input(INPUT_POST,"tipoRelatorio");
  $dataInicial = filter_input(INPUT_POST,"start");
  $dataFinal = filter_input(INPUT_POST,"end");

?>

<body>

  <div class=container-fluid>
    
    <div class=row>
      <div class='col-md-12'>
        <?php echo "<center><h1>Comissão da $equipe entre $dataInicial - $dataFinal </h1></center>";?>
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
            if($tipo == "Assistencia")
            {
              $sql_comissao = oci_parse($conn, "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA
                          FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c WHERE b.nome = '$equipe'
                          AND a.codequ = b.codequ AND a.codsere is not null  AND 
                          a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal'  AND a.codser = c.codser AND c.codser LIKE '3%' 
                          ORDER BY a.dtexec ASC");
            }elseif($tipo == "Instalação"){
              $sql_comissao = oci_parse($conn, "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA
                          FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c WHERE b.nome = '$equipe'
                          AND a.codequ = b.codequ AND a.codsere is not null  AND 
                          a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal'  AND a.codser = c.codser AND c.codser NOT LIKE '3%' 
                          ORDER BY a.dtexec ASC");
            }else{
              $sql_comissao = oci_parse($conn, "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA
                          FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c WHERE b.nome = '$equipe'
                          AND a.codequ = b.codequ AND a.codsere is not null  AND 
                          a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal'  AND a.codser = c.codser
                          ORDER BY a.dtexec ASC");
            }

            oci_execute($sql_comissao);
            $soma = 0.00;
            $quantidade_OS = 0;
            while ($resultado = oci_fetch_array($sql_comissao, OCI_BOTH))
            {
              if($tipo != "Assistência" and $resultado[9] == null )
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
                    $resultado[6] = 26.56 + ($resultado[8] * 18.00);
                  }elseif($resultado[7] == 1 AND $resultado[8] >=0)
                  {
                    $resultado[6] = 26.56 + ($resultado[8] * 18.00);
                  }elseif($resultado[7] < 1 AND $resultado[8] >=1) //;se for somente para instalar o ponto adicional.
                  {
                    $resultado[6] = 26.56 + (($resultado[8] - 1) * 18.00);
                  }
                }elseif(strpos($resultado[0],"DESCONEXAO") !== FALSE )
                {
                    $resultado[6] = 25;
                }
                elseif(strpos($resultado[0],"TRANSFERENCIA") != FALSE)
                {
                  if($resultado[7] > 1 AND $resultado[8] >= 0)
                  {
                    $resultado[7] = $resultado[7] - 1;
                    $resultado[8] = $resultado[8] + $resultado[7];
                    
                    if($resultado[7] !=1)
                    {
                        $resultado[7] = 1;
                    }
                    $resultado[6] = 65.86 + ($resultado[8] * 18.00);
                  }elseif($resultado[7] == 1 AND $resultado[8] >=0)
                  {
                    $resultado[6]= 65.86 + ($resultado[8] * 18.00 );
                  }elseif(strpos($resultado[0],"RECONEXAO") !== FALSE )
                  {
                    if($resultado[7] > 1 AND $resultado[8] >= 0)
                    {
                      $resultado[7] = $resultado[7] - 1;
                      $resultado[8] = $resultado[8] + $resultado[7];
                      if($resultado[7] !=1)
                      {
                          $resultado[7] = 1;
                      }
                      $resultado[6] = 65.86 + ($resultado[8] * 18.00);
                    }
                    elseif($resultado[7] == 1 AND $resultado[8] >=0)
                    {
                      $resultado[6]= 65.86 + ($resultado[8] * 18.00);
                    }elseif($resultado[7] > 1 AND $resultado[8] >= 0)
                    {
                      $resultado[7] = $resultado[7] - 1;
                      $resultado[8] = $resultado[8] + $resultado[7];
                      
                      if($resultado[7] != 1)
                      {
                        $resultado[7] = 1;
                      }
                      $resultado[6] = 65.86 + ($resultado[8] * 18);
                    }
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
                    $resultado[6] = 26.56 + ($resultado[8] * 18.00);
                  }elseif($resultado[7] == 1 AND $resultado[8] >=0)
                  {
                    $resultado[6] = 26.56 + ($resultado[8] * 18.00);
                  }
                }
                elseif(strpos($resultado[0],"DESCONEXAO ") !== FALSE )
                {
                  $resultado[6] = 25.00;
                }
                elseif(strpos($resultado[0],"TRANSFERENCIA") != FALSE)
                {
                  if($resultado[7] > 1 AND $resultado[8] >= 0)
                  {
                    $resultado[7] = $resultado[7] - 1;
                    $resultado[8] = $resultado[8] + $resultado[7];
                    if($resultado[7] !=1)
                    {
                        $resultado[7] = 1;
                    }
                    $resultado[6] = 38.24 + ($resultado[8] * 18.00);
                  }
                  elseif($resultado[7] == 1 AND $resultado[8] >=0)
                  {
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
                    $resultado[6] = 38.24 + ($resultado[8] * 18.00);
                  }
                  elseif($resultado[7] == 1 AND $resultado[8] >=0)
                  {
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
                  $resultado[6] =  38.24 + ($resultado[8] * 18.00);
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