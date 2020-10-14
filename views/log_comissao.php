<?php

include "../config/db.php";
include "../classes/header.php";
include "../classes/funcoes.php";

$logs = array();

$sql_log_comissao = ("SELECT numero_os, contrato, users.nome as nome, obs FROM os_comissao_detail log
                        INNER JOIN system_user users on log.user  = users.id");
$log_query = mysqli_query($conectar,$sql_log_comissao);

while ($row = mysqli_fetch_assoc($log_query))
{
    array_push($logs, array(
       "os" => $row['numero_os'],
       "contrato" =>$row['contrato'],
       "usuario" =>$row['nome'],
       "justificativa" =>$row['obs'],
    ));
}
?>

<body>
    <?php include "../classes/nav.php";?>

    <div id="main" class="container-fluid">
        <div id="top" class="row">
            <div class="col-md-3">
                <h2>Log Alterações de Comissão</h2>
            </div>
        </div>

        <div id="list" class="row">
            <div class="table-responsive col-md-12">
                <table class="table table-striped table-hover display" id='tabelaLog' cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th>Ordem de Serviço</th>
                            <th>Contrato</th>
                            <th>Autor</th>
                            <th>Justificativa</th>
                            <th class="actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($logs as $log) {
                                echo "
                                    <tr class='$log[os]'>
                                       <td>$log[os]</td>
                                       <td>$log[contrato]</td>
                                       <td>$log[usuario]</td>
                                       <td>$log[justificativa]</td>
                                       <td>
                                           <button class='btn btn-danger btn-xs' onClick='removeComissionLog($log[os])'>
                                              <span class='glyphicon glyphicon-remove' aria-hidden='true'>
                                              </span>
                                           </button>
                                        </td>
                                    </tr>
                                ";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="bottom" class="row">
    </div>
</body>

<?php include "../classes/footer.php";?>
