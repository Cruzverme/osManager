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

  $listaComissao = array();


$dataInicial = converteData($dataInicial);
$dataFinal = converteData($dataFinal);

$sql_comissao = oci_parse($conn, "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA, d.apto, c.codser,a.codsere
                          FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c, cplus.tva0900 d 
                          WHERE a.contra = d.contra AND b.nome = '$equipe' AND b.codcid = a.codcid AND a.codequ = b.codequ 
                          AND a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal' AND a.codser = c.codser AND
                          (c.codser LIKE '2%'  OR c.nome LIKE '%RETIRADA%')
                          ORDER BY a.dtexec ASC");

if ($tipo == "assistencia") {
    $sql_comissao = oci_parse($conn, "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA, d.apto
                          FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c, cplus.tva0900 d WHERE a.contra = d.contra AND
                          b.nome = '$equipe' AND b.codcid = a.codcid AND a.codequ = b.codequ AND a.codsere is not null  AND 
                          a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal'  AND a.codser = c.codser AND c.codcla <> 1
                          AND c.codser NOT LIKE '2%' AND c.nome NOT LIKE 'RETIRADA%'  
                          ORDER BY a.dtexec ASC");
} elseif ($tipo == "instalacao") {
    $sql_comissao = oci_parse($conn, "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA, d.apto
                          FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c, cplus.tva0900 d WHERE a.contra = d.contra AND
                          b.nome = '$equipe' AND b.codcid = a.codcid AND a.codequ = b.codequ AND a.codsere is not null  AND 
                          a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal'  AND a.codser = c.codser AND c.codcla = 1 
                          ORDER BY a.dtexec ASC");
}
$ok = oci_execute($sql_comissao);

$soma = 0.00;
$quantidade_OS = 0;
while ($resultado = oci_fetch_array($sql_comissao, OCI_BOTH)) {
    $nomeServico = "$resultado[0]-HFC";;
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
    $clienteFibra = verificaPacote($numeroContrato, $dataInicial, $dataFinal, $nomeEquipe);
    $pontosDoCliente = verificarPontos($numeroContrato, $numeroOS);

    if (sizeOf($clienteFibra) >= 1) {
        $nomeServico = "$resultado[0]-FTTH";
    }

    if (($tipo != "assistencia" and $tipo != "desconexao") and $numeroApto == null) { //SEM APARTAMENTO
        if (strpos($nomeServico, "CONEXAO PONTO ADICIONAL") !== FALSE) {
            if ($qtdPontoPrincipal > 1 and $qtdPontoSecundario >= 0) {
                $qtdPontoPrincipal = $qtdPontoPrincipal - 1;
                $qtdPontoSecundario = $qtdPontoSecundario + $qtdPontoPrincipal;

                if ($qtdPontoPrincipal != 1) {
                    $qtdPontoPrincipal = 1;
                }
                $valorComissao = 26.56 + ($qtdPontoSecundario * 18.00);//se for hfc
                $desativado = "disabled";

                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 30.00 + ($qtdPontoSecundario * 20.00);//se for fibra o valor e esse
                }
            } elseif ($qtdPontoPrincipal == 1 and $qtdPontoSecundario >= 0) {
                $valorComissao = 26.56 + ($qtdPontoSecundario * 18.00);
                $desativado = "disabled";

                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 30.00 + ($qtdPontoSecundario * 20.00);//se for fibra o valor e esse
                }
            } elseif ($qtdPontoPrincipal < 1 and $qtdPontoSecundario >= 1) { //;se for somente para instalar o ponto adicional.
                $valorComissao = 26.56 + (($qtdPontoSecundario - 1) * 18.00);
                $desativado = "disabled";

                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 30.00 + (($qtdPontoSecundario - 1) * 20.00);//diminui 1 porque 1 ponto tem o valor completo
                }
            }
        } elseif (strpos($nomeServico, "DESCONEXAO") !== FALSE) {
            $valorComissao = 25.00;
        } elseif (strpos($nomeServico, "TRANSFERENCIA") !== FALSE) {
            if ($qtdPontoPrincipal > 1 and $qtdPontoSecundario >= 0) {
                $qtdPontoPrincipal = $qtdPontoPrincipal - 1;
                $qtdPontoSecundario = $qtdPontoSecundario + $qtdPontoPrincipal;

                if ($qtdPontoPrincipal != 1) {
                    $qtdPontoPrincipal = 1;
                }
                $valorComissao = 65.86 + ($qtdPontoSecundario * 18.00);
                $desativado = "disabled";
                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 80.00 + ($qtdPontoSecundario * 20.00);
                }
            } elseif ($qtdPontoPrincipal == 1 and $qtdPontoSecundario >= 0) {
                $valorComissao = 65.86 + ($qtdPontoSecundario * 18.00);
                $desativado = "disabled";

                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 80.00 + ($qtdPontoSecundario * 20.00);
                }
            }
        } elseif (strpos($nomeServico, "RECONEXAO") !== FALSE) {
            if ($qtdPontoPrincipal > 1 and $qtdPontoSecundario >= 0) {
                $qtdPontoPrincipal = $qtdPontoPrincipal - 1;
                $qtdPontoSecundario = $qtdPontoSecundario + $qtdPontoPrincipal;

                if ($qtdPontoPrincipal != 1) {
                    $qtdPontoPrincipal = 1;
                }

                $valorComissao = 65.86 + ($qtdPontoSecundario * 18.00);
                $desativado = "disabled";

                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 80.00 + ($qtdPontoSecundario * 20.00);
                }
            } elseif ($qtdPontoPrincipal == 1 and $qtdPontoSecundario >= 0) {
                $valorComissao = 65.86 + ($qtdPontoSecundario * 18.00);
                $desativado = "disabled";

                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 80.00 + ($qtdPontoSecundario * 20.00);
                }
            }
        } elseif ($qtdPontoPrincipal > 1 and $qtdPontoSecundario >= 0) {
            $qtdPontoPrincipal = $qtdPontoPrincipal - 1;
            $qtdPontoSecundario = $qtdPontoSecundario + $qtdPontoPrincipal;

            if ($qtdPontoPrincipal != 1) {
                $qtdPontoPrincipal = 1;
            }

            $valorComissao = 65.86 + ($qtdPontoSecundario * 18);
            $desativado = "disabled";

            if (sizeOf($clienteFibra) >= 1) {
                $valorComissao = 80.00 + ($qtdPontoSecundario * 20.00);
            }
        } elseif ($qtdPontoPrincipal == 1 and sizeof($clienteFibra) >= 1) {
            $valorComissao = 80.00;
            $desativado = "disabled";

            if ($qtdPontoSecundario >= 0) {
                $valorComissao = 80.00 + ($qtdPontoSecundario * 20.00);//se for predio
            }
        } elseif ($qtdPontoPrincipal == 0 and sizeOf($clienteFibra) >= 1) {
            $qtdPontoPrincipal = $pontosDoCliente[0];
            $qtdPontoSecundario = 0;

            $valorComissao = 80;
            $desativado = "disabled";

            if ($pontosDoCliente[0] > 1) {
                $qtdPontoPrincipal = 1;
                $qtdPontoSecundario = $pontosDoCliente[0] - $qtdPontoPrincipal;
            }

            if ($qtdPontoSecundario > 0) {
                $valorComissao = 80 + ($qtdPontoSecundario * 20.00);
            }
        }
    }//FIM TIPO ASSISTENCIA
    elseif (($tipo != "assistencia" and $tipo != "desconexao") and $numeroApto != null) { // APARTAMENTOS
        if (strpos($nomeServico, "CONEXAO PONTO ADICIONAL") !== FALSE) {
            if ($qtdPontoPrincipal > 1 and $qtdPontoSecundario >= 0) {
                $qtdPontoPrincipal = $qtdPontoPrincipal - 1;
                $qtdPontoSecundario = $qtdPontoSecundario + $qtdPontoPrincipal;

                $valorComissao = 26.56 + ($qtdPontoSecundario * 18.00);
                $desativado = "disabled";

                if ($qtdPontoPrincipal != 1) {
                    $qtdPontoPrincipal = 1;
                }
                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 30.00 + ($qtdPontoSecundario * 20.00);
                }
            } elseif ($qtdPontoPrincipal == 1 and $qtdPontoSecundario >= 0) {
                $valorComissao = 26.56 + ($qtdPontoSecundario * 18.00);
                $desativado = "disabled";

                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 30.00 + ($qtdPontoSecundario * 20.00);
                }
            }
        } elseif (strpos($nomeServico, "DESCONEXAO ") !== FALSE) {
            $valorComissao = 25.00;
            $desativado = "disabled";
        } elseif (strpos($nomeServico, "TRANSFERENCIA") !== FALSE) {
            if ($qtdPontoPrincipal > 1 and $qtdPontoSecundario >= 0) {
                $qtdPontoPrincipal = $qtdPontoPrincipal - 1;
                $qtdPontoSecundario = $qtdPontoSecundario + $qtdPontoPrincipal;

                $valorComissao = 38.24 + ($qtdPontoSecundario * 18.00);
                $desativado = "disabled";

                if ($qtdPontoPrincipal != 1) {
                    $qtdPontoPrincipal = 1;
                }

                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 55.00 + ($qtdPontoSecundario * 20.00);//se for predio
                }
            } elseif ($qtdPontoPrincipal == 1 and $qtdPontoSecundario >= 0) {
                $valorComissao = 38.24 + ($qtdPontoSecundario * 18.00);
                $desativado = "disabled";

                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 55.00 + ($qtdPontoSecundario * 20.00);//se for predio
                    $desativado = "disabled";
                }
            }
        } elseif (strpos($nomeServico, "RECONEXAO") !== FALSE) {
            if ($qtdPontoPrincipal > 1 and $qtdPontoSecundario >= 0) {
                $qtdPontoPrincipal = $qtdPontoPrincipal - 1;
                $qtdPontoSecundario = $qtdPontoSecundario + $qtdPontoPrincipal;

                $valorComissao = 38.24 + ($qtdPontoSecundario * 18.00);
                $desativado = "disabled";

                if ($qtdPontoPrincipal != 1) {
                    $qtdPontoPrincipal = 1;
                }
                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 55.00 + ($qtdPontoSecundario * 20.00);//se for predio
                }
            } elseif ($qtdPontoPrincipal == 1 and $qtdPontoSecundario >= 0) {
                $valorComissao = 38.24 + ($qtdPontoSecundario * 18.00);
                $desativado = "disabled";

                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 55.00 + ($qtdPontoSecundario * 20.00);//se for predio fibra
                }
            }
        } elseif ($qtdPontoPrincipal > 1 and $qtdPontoSecundario >= 0) { //se primeira conexao predio
            $qtdPontoPrincipal = $qtdPontoPrincipal - 1;
            $qtdPontoSecundario = $qtdPontoSecundario + $qtdPontoPrincipal;

            $valorComissao = 38.24 + ($qtdPontoSecundario * 18.00);
            $desativado = "disabled";

            if ($qtdPontoPrincipal != 1) {
                $qtdPontoPrincipal = 1;
            }

            if (sizeOf($clienteFibra) >= 1) {
                $valorComissao = 55.00 + ($qtdPontoSecundario * 20.00);//se for predio
            }
        } elseif ($qtdPontoPrincipal == 1 and sizeOf($clienteFibra) >= 1) {
            $valorComissao = 55.00;
            $desativado = "disabled";

            //se tier ponto adicional
            if ($qtdPontoSecundario >= 0) {
                $valorComissao = 55.00 + ($qtdPontoSecundario * 20.00);//se for predio
            }
        } elseif ($qtdPontoPrincipal == 0 and sizeOf($clienteFibra) >= 1) { //se no Cplus vier zerado
            $qtdPontoPrincipal = $pontosDoCliente[0];
            $qtdPontoSecundario = 0;

            $valorComissao = 55.00;
            $desativado = "disabled";

            if ($pontosDoCliente[0] > 1) {
                $qtdPontoPrincipal = 1;
                $qtdPontoSecundario = $pontosDoCliente[0] - $qtdPontoPrincipal;
            }
            if ($qtdPontoSecundario > 0) {
                $valorComissao = 55.00 + ($qtdPontoSecundario * 20.00);
            }
        }
    }//FIM DE OUTROS SEM SER ASSISTENCIA (INSTALACAO)
    array_push($listaComissao, array(
        "nomeServico" => $nomeServico,
        "dataAgendamento" => $dataAgendamento,
        "dataExecucao" => $dataExecucao,
        "numeroOS" => $numeroOS,
        "numeroContrato" => $numeroContrato,
        "valorComissao" => $valorComissao,
        "qtdPontoPrincipal" => $qtdPontoPrincipal,
        "qtdPontoSecundario" => $qtdPontoSecundario,
        "numeroApto" => $numeroApto,
    ));
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
        <input type="hidden" name="listaComissao" value="<?php echo htmlspecialchars(serialize($listaComissao))?>">
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
            foreach ($listaComissao as $comissao) {
              echo "<tr>
                        <td>$comissao[nomeServico]</td>
                        <td>$comissao[dataAgendamento]</td>
                        <td>$comissao[dataExecucao]</td>
                        <td>$comissao[numeroOS]</td>
                        <td>$comissao[numeroContrato]</td>
                        <td>$comissao[valorComissao]</td>
                        <td>$comissao[qtdPontoPrincipal]</td>
                        <td>$comissao[qtdPontoSecundario]</td>
                        <td>$comissao[numeroApto]</td>
                      <td>
                          <button class='btn btn-default' onClick = ajustarValorComissao($comissao[numeroOS]) $desativado>
                            <span class='glyphicon glyphicon-cog'></span>
                          </button>
                      </td>
                    </tr>";

                    $quantidade_OS+=1;
                    $valor_comissao = str_replace(',','.',$comissao['valorComissao']);
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
