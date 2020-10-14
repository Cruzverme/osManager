<?php
    session_start();

    $ordemServico = filter_input(INPUT_POST,"os");

    function removeLog($ordemServico) {
        include "../config/db.php";

        $result = array(
            "success" => false,
            "message" => "erro ao remover LOG",
            "status" => 400,
        );

        $sql_deletarLog = "DELETE FROM os_comissao_detail WHERE numero_os = $ordemServico";
        $executa_alteracao = mysqli_query($conectar,$sql_deletarLog);

        if(mysqli_affected_rows($conectar) > 0) {
            $result = array(
                "success" => true,
                "status" => 200,
                "message" => "Log Removido",
            );
        }
        echo  json_encode($result, true);
    }

    removeLog($ordemServico);
