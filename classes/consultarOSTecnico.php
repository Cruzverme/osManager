<?php 

  include "../config/db.php";

  $nomeTecnico = filter_input(INPUT_POST,"nome");
  
  if($nomeTecnico){

    // date_default_timezone_set('America/Sao_Paulo');
    // $date = date('Y-m-d');

    $sql = "SELECT ordem.numero_os,ordem.data FROM os ordem
            INNER JOIN ordensservicos ordemEnviada ON ordemEnviada.ordemServico != ordem.numero_os 
            AND ordemEnviada.tecnico = ordem.tecnico
            WHERE ordem.tecnico = '$nomeTecnico' AND ordem.numero_os != ordemEnviada.ordemServico";

    $execQuery = mysqli_query($conectar,$sql);
    $numero = "";
    while($row = mysqli_fetch_array($execQuery))
    {
      $numero = mysqli_num_rows($execQuery);
      if(mysqli_num_rows($execQuery) >= 1)
        echo "<li class='list-group-item d-flex justify-content-between align-items-center listaDoTecnico'><strong>Número OS: </strong>$row[0] <strong>
        Designada Em:</strong> $row[1]</li>";
    }

    if($numero == "")
    echo "<li class='list-group-item d-flex justify-content-between align-items-center listaDoTecnico'>Não Há OS Pendente Para Executar</li>";
  }

?>