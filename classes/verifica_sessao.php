<?php 
// Inicia sessões 
session_start(); 

$user_ativo = $_SESSION["id_usuario"];
$name_ativo = $_SESSION["nome_usuario"];
$permissao = $_SESSION["nivel"];
$permiteEditarOS = $_SESSION["editOS"];

// Verifica se existe os dados da sessão de login 
if(!isset($user_ativo) || !isset($name_ativo))   
{ 
    //destroi sessao por mera segurança
    session_destroy();
    // Usuário não logado! Redireciona para a página de login 
    header("Location: ../index.php"); 
    exit; 
}
?> 