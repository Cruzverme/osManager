<?php

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

function verificaPacote($contrato,$dataI,$dataF,$equipe)
{
    include "../config/db_oracle.php";
    $sql = "select DISTINCT pontos.contra, osBaixada.nome,ordemServ.dtexec from cplus.tva1600 pontos 
      INNER JOIN cplus.tva1920 equipe ON equipe.ativo = 'S' AND equipe.nome LIKE '$equipe' 
      INNER JOIN cplus.tva1700 ordemServ ON ordemServ.codequ = equipe.codequ AND 
      ordemServ.DTEXEC BETWEEN '$dataI' and '$dataF' 
      INNER JOIN cplus.tva1000 nomePonto ON (nomePonto.nome LIKE '%FIBRA%' OR nomePonto.nome LIKE '%IPTV%') AND nomePonto.codprog = pontos.codprog 
      INNER JOIN cplus.tva2000 osBaixada ON osBaixada.codser = ordemServ.codser   
      WHERE pontos.codsit=27 AND pontos.contra=$contrato AND pontos.contra = ordemServ.contra 
      ORDER BY ordemServ.dtexec ASC";

    $prepara_query = oci_parse($conn,$sql);
    oci_execute($prepara_query);
    $array = array();
    while ($resultado = oci_fetch_array($prepara_query, OCI_BOTH))
    {
        array_push($array,$resultado[0]);
    }
    return $array;
}

function verificarPontos($contrato,$ordemServico)
{
    include "../config/db_oracle.php";

    $sql = "select count(pontos.contra) from cplus.tva1600 pontos
    join cplus.tva1700 os on pontos.contra = os.contra 
    where os.os = $ordemServico and pontos.codsit = 27 and pontos.contra = $contrato and pontos.codprog != 325 ";

    $prepara_query = oci_parse($conn,$sql);
    oci_execute($prepara_query);
    $array = array();
    while ($resultado = oci_fetch_array($prepara_query, OCI_BOTH))
    {
        array_push($array,$resultado[0]);
    }
    return $array;
}

function verificaStatusOS()
{
    include "../config/db_oracle.php";
    include "../config/db.php";

    $sql = ("SELECT ordemServico,tecnico FROM ordensServicos WHERE status <> 1");
    $result = mysqli_query($conectar,$sql);
    mysqli_num_rows($result);
    if(mysqli_num_rows($result) != 0)
    {
        while($resultado = mysqli_fetch_array($result))
        {
            $sql_os_cplus = "SELECT count(os.OS) from cplus.tva1700 os where os.os = $resultado[0] AND os.status = 'B' ";
            $cplus_os = oci_parse($conn,$sql_os_cplus);
            oci_execute($cplus_os);
            $linha = oci_fetch_array($cplus_os);
            $numero_linhas = $linha[0];
            if($numero_linhas > 0)
            {
                $sql = "UPDATE os SET os_concluida = 1 WHERE numero_os = $resultado[0] AND tecnico='$resultado[1]' ";
                $sql2 = "UPDATE ordensServicos SET status = 1 WHERE ordemServico = $resultado[0] AND tecnico='$resultado[1]'";
                mysqli_query($conectar,$sql);
                mysqli_query($conectar,$sql2);
            }
        }
    }
}

/**
 * @param $contrato
 * @return mixed
 */
function verificaPontosFTTH($contrato)
{
    include "../config/db_oracle.php";

    $sql= "SELECT count(pontos) FROM cplus.tva1600 pontos
            INNER JOIN cplus.tva1000 nomePonto ON (nomePonto.nome LIKE '%FIBRA%' OR nomePonto.nome LIKE '%IPTV%') AND 
            nomePonto.nome NOT LIKE '%FONE%'
            AND nomePonto.codprog = pontos.codprog 
            WHERE contra = $contrato";

    $quantidade_pontos_ftth = oci_parse($conn,$sql);
    oci_execute($quantidade_pontos_ftth);
    $linha = oci_fetch_array($quantidade_pontos_ftth);
    $numero_linhas = $linha[0];

    return $numero_linhas;
}

function isMigration($frase)
{
    $lista = ["MIGRA","MIGRAR", "MIGRACAO", "MIGRAÇÃO"];

    foreach($lista as $palavra) {
        if (strpos($frase, $palavra) !== false) {
            return true;
        }
    }
}

/**
 * @param $contrato
 * @return array|false
 */
function apartType($contrato)
{
    include "../config/db_oracle.php";

    $sql = "SELECT e.TIPPRUMA FROM CPLUS.tva0400 e INNER JOIN CPLUS.TVA0900 t ON CONTRA = '$contrato' WHERE e.CODRUA = t.CODRUA AND e.CODBAI = t.CODBAI AND e.CODCID = t.CODCID AND e.CODQUA = t.CODQUA AND e.NENDE = t.NENDE AND rownum = 1";

    $edificoQuery = oci_parse($conn,$sql);
    oci_execute($edificoQuery);
    $linha = oci_fetch_assoc($edificoQuery);

    return $linha['TIPPRUMA'];
}

/**
 * @param $equipe
 * @param $dataInicial
 * @param $dataFinal
 * @param $tipo
 * @return array
 */
function getOsDetails($equipe, $dataInicial, $dataFinal, $tipo) {
    include "../config/db_oracle.php";

    $dataInicial = converteData($dataInicial);
    $dataFinal = converteData($dataFinal);
    $listaComissao = array();

    $sql_comissao = OsComissionSQL($equipe, $dataInicial, $dataFinal, $tipo);

    $sql_query_comissao = oci_parse($conn, $sql_comissao);

    oci_execute($sql_query_comissao);

    while ($resultado = oci_fetch_array($sql_query_comissao, OCI_BOTH)) {
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
        $observacao1 = $resultado[10];
        $observacao2 = $resultado[11];

        $desativado = "";
        $clienteFibra = verificaPacote($numeroContrato,$dataInicial,$dataFinal,$nomeEquipe);

        if (sizeOf($clienteFibra) >= 1) {
            $nomeServico = "$resultado[0]-FTTH";
        }
        $isEdited = isOSEdited($numeroOS);
        $valorComissaoInCplus = $valorComissao;
        $editedObs = "";
        if ($tipo != "assistencia" and $tipo != "desconexao") {
            if (strpos($nomeServico,"CONEXAO PONTO ADICIONAL") !== FALSE) {
                $dadosConexaoPontoAdicional = checkComissionInConexaoPontoAdicional($qtdPontoPrincipal, $qtdPontoSecundario, $clienteFibra, $valorComissao);

                $qtdPontoPrincipal = $dadosConexaoPontoAdicional['qtdPontoPrincipal'];
                $qtdPontoSecundario = $dadosConexaoPontoAdicional['qtdPontoSecundario'];
                $valorComissao = $dadosConexaoPontoAdicional['valorComissao'];

                if ($isEdited) {
                    $valorComissao = $valorComissaoInCplus;
                    $editedObs = $isEdited;
                }
            } elseif (strpos($nomeServico,"DESCONEXAO") !== FALSE ) {
                $valorComissao = 25.00;
            } elseif (strpos($nomeServico,"TRANSFERENCIA") !== FALSE) {
                $dadosConexaoPontoAdicional = checkComissionInTransferencia_Reconexao($qtdPontoPrincipal, $qtdPontoSecundario, $clienteFibra, $valorComissao, $numeroContrato);

                $qtdPontoPrincipal = $dadosConexaoPontoAdicional['qtdPontoPrincipal'];
                $qtdPontoSecundario = $dadosConexaoPontoAdicional['qtdPontoSecundario'];
                $valorComissao = $dadosConexaoPontoAdicional['valorComissao'];

                if ($isEdited) {
                    $valorComissao = $valorComissaoInCplus;
                    $editedObs = $isEdited;
                }
            } elseif (strpos($nomeServico,"RECONEXAO") !== FALSE ) {
                $dadosConexaoPontoAdicional = checkComissionInTransferencia_Reconexao($qtdPontoPrincipal, $qtdPontoSecundario, $clienteFibra, $valorComissao, $numeroContrato);

                $qtdPontoPrincipal = $dadosConexaoPontoAdicional['qtdPontoPrincipal'];
                $qtdPontoSecundario = $dadosConexaoPontoAdicional['qtdPontoSecundario'];
                $valorComissao = $dadosConexaoPontoAdicional['valorComissao'];

                if ($isEdited) {
                    $valorComissao = $valorComissaoInCplus;
                    $editedObs = $isEdited;
                }
            } elseif (strpos($nomeServico,"DE CABEAMENTO") !== FALSE) {
                $dadosConexaoPontoAdicional = checkComissionInDeCabeamento($qtdPontoPrincipal, $qtdPontoSecundario, $clienteFibra, $valorComissao, $numeroContrato, $observacao1, $observacao2);

                $qtdPontoPrincipal = $dadosConexaoPontoAdicional['qtdPontoPrincipal'];
                $qtdPontoSecundario = $dadosConexaoPontoAdicional['qtdPontoSecundario'];
                $valorComissao = $dadosConexaoPontoAdicional['valorComissao'];

                if ($isEdited) {
                    $valorComissao = $valorComissaoInCplus;
                    $editedObs = $isEdited;
                }
            } elseif ($qtdPontoPrincipal > 1 and $qtdPontoSecundario >= 0) { //PRIMEIRA CONEXAO
                $dadosConexaoPontoAdicional = checkComissionInPrimeiraConexaoComMultiploPontoPrincipal($qtdPontoPrincipal, $qtdPontoSecundario, $clienteFibra, $numeroContrato);

                $qtdPontoPrincipal = $dadosConexaoPontoAdicional['qtdPontoPrincipal'];
                $qtdPontoSecundario = $dadosConexaoPontoAdicional['qtdPontoSecundario'];
                $valorComissao = $dadosConexaoPontoAdicional['valorComissao'];

                if ($isEdited) {
                    $valorComissao = $valorComissaoInCplus;
                    $editedObs = $isEdited;
                }
            } elseif ($qtdPontoPrincipal == 1 and sizeof($clienteFibra) >=1) {
                $dadosConexaoPontoAdicional = checkComissionInPrimeiraConexaoComUnicoPontoPrincipalFTTH($qtdPontoSecundario, $numeroContrato);

                $valorComissao = $dadosConexaoPontoAdicional['valorComissao'];

                if ($isEdited) {
                    $valorComissao = $valorComissaoInCplus;
                    $editedObs = $isEdited;
                }
            } elseif ($qtdPontoPrincipal == 0 and sizeOf($clienteFibra)>=1) { //se no Cplus vier zerado
                $dadosConexaoPontoAdicional = checkComissionInPrimeiraConexaoComZeroPontoPrincipalFTTH($numeroContrato, $numeroOS);

                $qtdPontoPrincipal = $dadosConexaoPontoAdicional['qtdPontoPrincipal'];
                $qtdPontoSecundario = $dadosConexaoPontoAdicional['qtdPontoSecundario'];
                $valorComissao = $dadosConexaoPontoAdicional['valorComissao'];

                if ($isEdited) {
                    $valorComissao = $valorComissaoInCplus;
                    $editedObs = $isEdited;
                }
            }
        }
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
            "desativado" => $desativado,
            "obsEdited" => $editedObs,
        ));
    }

    return $listaComissao;
}

/**
 * @param $equipe
 * @param $dataInicial
 * @param $dataFinal
 * @param $tipo
 * @return string
 */
function OsComissionSQL($equipe, $dataInicial, $dataFinal, $tipo)
{
    $sql_comissao = "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA, d.apto, a.obser1, a.obser2
                              FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c, cplus.tva0900 d 
                              WHERE a.contra = d.contra AND b.nome = '$equipe' AND b.codcid = a.codcid AND a.codequ = b.codequ 
                              AND a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal' AND a.codser = c.codser AND
                              (c.codser LIKE '2%'  OR c.nome LIKE '%RETIRADA%')
                              ORDER BY a.dtexec, a.contra ASC";

    if ($tipo == "assistencia") {
        $sql_comissao = "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA, d.apto, a.obser1, a.obser2
                              FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c, cplus.tva0900 d WHERE a.contra = d.contra AND
                              b.nome = '$equipe' AND b.codcid = a.codcid AND a.codequ = b.codequ AND a.codsere is not null  AND 
                              a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal'  AND a.codser = c.codser AND c.codcla <> 1
                              AND c.codser NOT LIKE '2%' AND c.nome NOT LIKE 'RETIRADA%'  
                              ORDER BY a.dtexec, a.contra ASC";
    } elseif($tipo == "instalacao") {
        $sql_comissao = "SELECT c.nome, a.dtagen,a.DTEXEC, b.nome, a.os, a.contra, a.vlcom, a.NROPP, a.NROPA, d.apto, a.obser1, a.obser2
                              FROM cplus.tva1700 a, cplus.tva1920 b, cplus.tva2000 c, cplus.tva0900 d WHERE a.contra = d.contra AND
                              b.nome = '$equipe' AND b.codcid = a.codcid AND a.codequ = b.codequ AND a.codsere is not null  AND 
                              a.DTEXEC BETWEEN '$dataInicial' and '$dataFinal'  AND a.codser = c.codser AND c.codcla = 1 
                              ORDER BY a.dtexec, a.contra ASC";
    }

    return $sql_comissao;
}

/**
 * @param $qtdPontoPrincipal
 * @param $qtdPontoSecundario
 * @param $clienteFibra
 * @param $valorComissao
 * @return array
 */
function checkComissionInConexaoPontoAdicional($qtdPontoPrincipal, $qtdPontoSecundario, $clienteFibra, $valorComissao)
{
    if ($qtdPontoPrincipal > 1 and $qtdPontoSecundario >=0) {
        $qtdPontoPrincipal = $qtdPontoPrincipal - 1;
        $qtdPontoSecundario = $qtdPontoSecundario + $qtdPontoPrincipal;

        if ($qtdPontoPrincipal !=1) {
            $qtdPontoPrincipal = 1;
        }
        $valorComissao = 26.56 + ($qtdPontoSecundario * 18.00);//se for hfc

        if (sizeOf($clienteFibra) >= 1) {
            $valorComissao = 30.00 + ($qtdPontoSecundario * 20.00);//se for fibra o valor e esse
        }
    } elseif ($qtdPontoPrincipal == 1 and $qtdPontoSecundario >=0) {
        $valorComissao = 26.56 + ($qtdPontoSecundario * 18.00);

        if (sizeOf($clienteFibra) >= 1) {
            $valorComissao = 30.00 + ($qtdPontoSecundario * 20.00);//se for fibra o valor e esse
        }
    } elseif ($qtdPontoPrincipal < 1 and $qtdPontoSecundario >=1) { //;se for somente para instalar o ponto adicional.
        $valorComissao = 26.56 + (($qtdPontoSecundario - 1) * 18.00);

        if (sizeOf($clienteFibra) >= 1) {
            $valorComissao = 30.00 + (($qtdPontoSecundario - 1) * 20.00);//diminui 1 porque 1 ponto tem o valor completo
        }
    }

    return array(
        "valorComissao" => $valorComissao,
        "qtdPontoPrincipal" => $qtdPontoPrincipal,
        "qtdPontoSecundario" => $qtdPontoSecundario
    );
}

/**
 * @param $qtdPontoPrincipal
 * @param $qtdPontoSecundario
 * @param $clienteFibra
 * @param $valorComissao
 * @param $contrato
 * @return array
 */
function checkComissionInTransferencia_Reconexao($qtdPontoPrincipal, $qtdPontoSecundario, $clienteFibra, $valorComissao, $contrato)
{
    if($qtdPontoPrincipal > 1 and $qtdPontoSecundario >= 0) {
        $qtdPontoPrincipal = $qtdPontoPrincipal - 1;
        $qtdPontoSecundario = $qtdPontoSecundario + $qtdPontoPrincipal;

        if ($qtdPontoPrincipal !=1) {
            $qtdPontoPrincipal = 1;
        }

        $valorComissao = 65.00 + ($qtdPontoSecundario * 18.00);

        if (sizeOf($clienteFibra) >= 1) {
            $valorComissao = 80.00 + ($qtdPontoSecundario * 20.00);
        }

        if (apartType($contrato) == 'E') { //se for predio com backbone
            $valorComissao = 38.00 + ($qtdPontoSecundario * 18.00);

            if (sizeOf($clienteFibra) >= 1) {
                $valorComissao = 55.00 + ($qtdPontoSecundario * 20.00);
            }
        }
    } elseif ($qtdPontoPrincipal == 1 and $qtdPontoSecundario >=0) {
        $valorComissao= 65.00 + ($qtdPontoSecundario * 18.00 );

        if (sizeOf($clienteFibra) >= 1) {
            $valorComissao = 80.00 + ($qtdPontoSecundario * 20.00);
        }

        if (apartType($contrato) == 'E') { //se for predio com backbone
            $valorComissao = 38.00 + ($qtdPontoSecundario * 18.00);

            if (sizeOf($clienteFibra) >= 1) {
                $valorComissao = 55.00 + ($qtdPontoSecundario * 20.00);
            }
        }
    }

    return array(
        "valorComissao" => $valorComissao,
        "qtdPontoPrincipal" => $qtdPontoPrincipal,
        "qtdPontoSecundario" => $qtdPontoSecundario
    );
}

/**
 * @param $qtdPontoPrincipal
 * @param $qtdPontoSecundario
 * @param $clienteFibra
 * @param $valorComissao
 * @param $contrato
 * @param $observacao1
 * @param $observacao2
 * @return array
 */
function checkComissionInDeCabeamento($qtdPontoPrincipal, $qtdPontoSecundario, $clienteFibra, $valorComissao, $contrato, $observacao1, $observacao2)
{
    $quantidadePontoFTTH = verificaPontosFTTH($contrato);

    if (isMigration($observacao1) || isMigration($observacao2)) {
        if ($qtdPontoPrincipal < 1) {
            $qtdPontoPrincipal = 1;

            if ($quantidadePontoFTTH > 1) {
                $qtdPontoSecundario = $quantidadePontoFTTH - $qtdPontoPrincipal;
            }
        }

        if($qtdPontoPrincipal > 1 and $qtdPontoSecundario >= 0) {
            $qtdPontoPrincipal = $qtdPontoPrincipal - 1;
            $qtdPontoSecundario = $qtdPontoSecundario + $qtdPontoPrincipal;

            if($qtdPontoPrincipal !=1) {
                $qtdPontoPrincipal = 1;
            }

            $valorComissao = 65.00 + ($qtdPontoSecundario * 18.00);

            if (sizeOf($clienteFibra) >= 1) {
                $valorComissao = 65.00 + ($qtdPontoSecundario * 20.00);
            }

            if (apartType($contrato) == 'E') { //se for predio com backbone
                $valorComissao = 38.00 + ($qtdPontoSecundario * 18.00);

                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 38.00 + ($qtdPontoSecundario * 20.00);
                }
            }
        } elseif($qtdPontoPrincipal == 1 and $qtdPontoSecundario >=0) {
            $valorComissao= 65.00 + ($qtdPontoSecundario * 18.00);

            if (sizeOf($clienteFibra) >= 1) {
                $valorComissao = 65.00 + ($qtdPontoSecundario * 20.00);
            }

            if (apartType($contrato) == 'E') { //se for predio com backbone
                $valorComissao = 38.00 + ($qtdPontoSecundario * 18.00);

                if (sizeOf($clienteFibra) >= 1) {
                    $valorComissao = 38.00 + ($qtdPontoSecundario * 20.00);
                }
            }
        }
    }

    return array(
        "valorComissao" => $valorComissao,
        "qtdPontoPrincipal" => $qtdPontoPrincipal,
        "qtdPontoSecundario" => $qtdPontoSecundario
    );
}

/**
 * @param $qtdPontoPrincipal
 * @param $qtdPontoSecundario
 * @param $clienteFibra
 * @param $contrato
 * @return array
 */
function checkComissionInPrimeiraConexaoComMultiploPontoPrincipal($qtdPontoPrincipal, $qtdPontoSecundario, $clienteFibra, $contrato)
{
    $qtdPontoPrincipal = $qtdPontoPrincipal - 1;
    $qtdPontoSecundario = $qtdPontoSecundario + $qtdPontoPrincipal;

    if ($qtdPontoPrincipal != 1) {
        $qtdPontoPrincipal = 1;
    }

    $valorComissao = 65.00 + ($qtdPontoSecundario * 18);

    if (sizeOf($clienteFibra) >= 1) {
        $valorComissao = 80.00 + ($qtdPontoSecundario * 20.00);
    }

    if (apartType($contrato) == 'E') { //se for predio com backbone
        $valorComissao = 38.00 + ($qtdPontoSecundario * 18.00);

        if (sizeOf($clienteFibra) >= 1) {
            $valorComissao = 55.00 + ($qtdPontoSecundario * 20.00);
        }
    }

    return array(
        "valorComissao" => $valorComissao,
        "qtdPontoPrincipal" => $qtdPontoPrincipal,
        "qtdPontoSecundario" => $qtdPontoSecundario
    );


}

/**
 * @param $qtdPontoSecundario
 * @param $contrato
 * @return float[]
 */
function checkComissionInPrimeiraConexaoComUnicoPontoPrincipalFTTH($qtdPontoSecundario, $contrato)
{
    $valorComissao = 80.00;

    if ($qtdPontoSecundario >= 0) {
        $valorComissao = 80.00 + ($qtdPontoSecundario * 20.00);//se for predio
    }

    if (apartType($contrato) == 'E') { //se for predio com backbone
        $valorComissao = 55.00;

        if ($qtdPontoSecundario >= 0) {
            $valorComissao = 55.00 + ($qtdPontoSecundario * 20.00);//se for predio
        }
    }

    return array(
        "valorComissao" => $valorComissao,
    );


}

/**
 * @param $contrato
 * @param $numeroOS
 * @return array
 */
function checkComissionInPrimeiraConexaoComZeroPontoPrincipalFTTH($contrato, $numeroOS)
{
    $pontosDoCliente = verificarPontos($contrato,$numeroOS);

    $qtdPontoPrincipal = $pontosDoCliente[0];
    $qtdPontoSecundario = 0;

    $valorComissao = 80;

    if ($pontosDoCliente[0] > 1) {
        $qtdPontoPrincipal = 1;
        $qtdPontoSecundario = $pontosDoCliente[0] - $qtdPontoPrincipal;
    }

    if ($qtdPontoSecundario > 0) {
        $valorComissao = 80 + ($qtdPontoSecundario * 20.00);
    }

    if (apartType($contrato) == 'E') { //se for predio com backbone
        $valorComissao = 55.00;

        if ($qtdPontoSecundario > 0) {
            $valorComissao = 55.00 + ($qtdPontoSecundario * 20.00);
        }
    }

    return array(
        "valorComissao" => $valorComissao,
        "qtdPontoPrincipal" => $qtdPontoPrincipal,
        "qtdPontoSecundario" => $qtdPontoSecundario
    );


}

/**
 * @param $ordemServico
 * @return bool
 */
function isOSEdited($ordemServico)
{
    include "../config/db.php";

    $sql_salvar_obs_os = "SELECT obs FROM os_comissao_detail WHERE numero_os=$ordemServico";

    $result_query = mysqli_query($conectar, $sql_salvar_obs_os);

    if (mysqli_affected_rows($conectar) > 0) {
        $row = mysqli_fetch_assoc($result_query);
        return $row['obs'];
    }

    return false;
}

//  $ok = verificaStatusOS();
//  $ok = verificarPontos(29431,561113);

//  $ok =  verificaPacote(452,'01/OCT/2018','23/OCT/2018');//42293,'01/OCT/2018','31/OCT/2018');
//  var_dump($ok);
//  echo $ok[0];
//   echo sizeOf($ok);
//  if(sizeOf($ok) >= 1)
//    echo "eaeae";
//  else
//    print_r($ok);//echo $ok[0];
?>
