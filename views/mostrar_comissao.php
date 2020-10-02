<?php 
  include "../config/db.php";
  include "../classes/header.php";
  include "../config/db_oracle.php";
  include "../classes/funcoes.php";

  $equipe = filter_input(INPUT_POST,"equipe");
  $tipo = filter_input(INPUT_POST,"tipoRelatorio");
  $dataInicial = filter_input(INPUT_POST,"start");
  $dataFinal = filter_input(INPUT_POST,"end");

  switch($tipo)
  {
    case 'assistencia': $labelTipo = 'Assistência';break;
    case 'instalacao': $labelTipo = 'Instalação';break;
    case 'desconexao': $labelTipo = 'Desconexão';break;

  } 
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
        <?php echo "<center><h1>Comissão de $labelTipo da $equipe entre $dataInicial - $dataFinal </h1></center>";?>
      </div>
    </div>
    <div class=row>
     <form action="pdf.php" target="_blank" method="POST">
        <div class='col-md-12'>
          <button type='submit' class='btn btn-info pull-right'> <span class='glyphicon glyphicon-save-file'>  GERAR PDF</span></button>
        <div>
        <input type="hidden" name="tipoRelatorio" value="<?php echo $tipo ?>" />
        <input type="hidden" name="equipe" value="<?php echo $equipe ?>" />
        <input type="hidden" name="start" value="<?php echo $dataInicial ?>" />
        <input type="hidden" name="end" value="<?php echo $dataFinal ?>" />
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
            $dataInicial = converteData($dataInicial);
            $dataFinal = converteData($dataFinal);

            if($tipo == "assistencia")
            {
              $sql_comissao = oci_parse($conn, "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA, d.apto
                          FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c, cplus.tva0900 d WHERE a.contra = d.contra AND
                          b.nome = '$equipe' AND b.codcid = a.codcid AND a.codequ = b.codequ AND a.codsere is not null  AND 
                          a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal'  AND a.codser = c.codser AND c.codcla <> 1
                          AND c.codser NOT LIKE '2%' AND c.nome NOT LIKE 'RETIRADA%'  
                          ORDER BY a.dtexec ASC");
            }elseif($tipo == "instalacao"){
              $sql_comissao = oci_parse($conn, "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA, d.apto
                          FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c, cplus.tva0900 d WHERE a.contra = d.contra AND
                          b.nome = '$equipe' AND b.codcid = a.codcid AND a.codequ = b.codequ AND a.codsere is not null  AND 
                          a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal'  AND a.codser = c.codser AND c.codcla = 1 
                          ORDER BY a.dtexec ASC");
            }else{
              $sql_comissao = oci_parse($conn, "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA, d.apto, c.codser,a.codsere
                          FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c, cplus.tva0900 d 
                          WHERE a.contra = d.contra AND b.nome = '$equipe' AND b.codcid = a.codcid AND a.codequ = b.codequ 
                          AND a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal' AND a.codser = c.codser AND
                          (c.codser LIKE '2%'  OR c.nome LIKE '%RETIRADA%')
                          ORDER BY a.dtexec ASC");
            }
           $ok = oci_execute($sql_comissao);
          
            $soma = 0.00;
            $quantidade_OS = 0;
            while ($resultado = oci_fetch_array($sql_comissao, OCI_BOTH))
            {
                $nomeServico = $resultado[0];
                $dataAgendamento = $resultado[1];
                $dataExecucao = $resultado[2];
                $nomeEquipe = $resultado[3];
                $numeroOS = $resultado[4];
                $numeroContrato = $resultado[5];
                $valorComissao = $resultado[6];
                $qtdPontoPrincipal = $resultado[7];
                $qtdPontoSecundario = $resultado[8];
                $numeroApto = $resultado[9];

              $desativado = "";
              $clienteFibra = verificaPacote($numeroContrato,$dataInicial,$dataFinal,$nomeEquipe);
              $pontosDoCliente = verificarPontos($numeroContrato,$numeroOS);
              if(sizeOf($clienteFibra) >= 1)
              {
                  $nomeServico = "$resultado[0]-FTTH";
              }else{
                  $nomeServico = "$resultado[0]-HFC";
              }
              if(($tipo != "assistencia" AND $tipo != "desconexao") and $resultado[9] == null ) //SEM APARTAMENTO
              {
                if(strpos($nomeServico,"CONEXAO PONTO ADICIONAL") !== FALSE)
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
                }elseif(strpos($nomeServico,"DESCONEXAO") !== FALSE )
                {
                    $resultado[6] = 25.00;
                }
                elseif(strpos($nomeServico,"TRANSFERENCIA") !== FALSE)
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
                elseif(strpos($nomeServico,"RECONEXAO") !== FALSE )
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
              elseif(($tipo != "assistencia" AND $tipo != "desconexao")and $resultado[9] != null)
              {
                if(strpos($nomeServico,"CONEXAO PONTO ADICIONAL") !== FALSE)
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
                elseif(strpos($nomeServico,"DESCONEXAO ") !== FALSE )
                {
                  $resultado[6] = 25.00;
                  $desativado = "disabled";
                }
                elseif(strpos($nomeServico,"TRANSFERENCIA") !== FALSE)
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
                elseif(strpos($nomeServico,"RECONEXAO") !== FALSE )
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
              echo "<tr>
                      <td>$nomeServico</td>
                      <td>$resultado[1]</td>
                      <td>$resultado[2]</td>
                      <td>$resultado[4]</td>
                      <td>$resultado[5]</td>
                      <td>$resultado[6]</td>
                      <td>$resultado[7]</td>
                      <td>$resultado[8]</td>
                      <td>$resultado[9]</td>
                      <td>
                          <button class='btn btn-default' onClick = ajustarValorComissao($resultado[4]) $desativado>
                            <span class='glyphicon glyphicon-cog'></span>
                          </button>
                      </td>
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

<!-- MODAL -->
    <div class="form-content" style="display:none;">
      <form class="form" role="form">
        <div class="form-group">
          <label for="campoValorComissao">Valor da Comissão</label>
          <input type="number" min="0.00" step=any id="campoValorComissao" name="valor_comissao" placeholder="Insira a comissão" class="form-control">
        </div>
      </form>  
    </div>

<?php include "../classes/footer.php";?>
