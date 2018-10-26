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


 // $dataI='01/OCT/2018';
 // $dataF='31/OCT/2018';
 // $contrato=42293;

  function verificaPacote($contrato,$dataI,$dataF,$equipe)
  {
    include "../config/db_oracle.php";
    $sql = "select DISTINCT pontos.contra, osBaixada.nome,ordemServ.dtexec from cplus.tva1600 pontos 
      INNER JOIN cplus.tva1920 equipe ON equipe.ativo = 'S' AND equipe.nome LIKE '$equipe' 
      INNER JOIN cplus.tva1700 ordemServ ON ordemServ.codequ = equipe.codequ AND 
      ordemServ.DTEXEC BETWEEN '$dataI' and '$dataF' 
      INNER JOIN cplus.tva1000 nomePonto ON (nomePonto.nome LIKE '%FIBRA%' OR nomePonto.nome LIKE '%IPTV%') AND nomePonto.codprog = pontos.codprog 
      INNER JOIN cplus.tva2000 osBaixada ON osBaixada.codser = ordemServ.codser  AND osBaixada.codser NOT LIKE '3%' 
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
//  $ok = verificarPontos(29431,561113);
    
//  $ok =  verificaPacote(452,'01/OCT/2018','23/OCT/2018');//42293,'01/OCT/2018','31/OCT/2018');
//  echo $ok[0];
//   echo sizeOf($ok);
//  if(sizeOf($ok) >= 1)
//    echo "eaeae";
//  else
//    print_r($ok);//echo $ok[0];
?>
