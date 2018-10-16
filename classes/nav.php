<?php 

echo "
<nav class='navbar navbar-inverse navbar-fixed-top'>
  <div class='container-fluid'>
    <div class='navbar-header'>
      <button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#navbar' aria-expanded=false aria-controls=navbar>
        <span class=sr-only>Toggle navigation</span>
        <span class=icon-bar></span>
        <span class=icon-bar></span>
        <span class=icon-bar></span>
      </button>
      <a class='navbar-brand' href=#>Gerenciador de Ordem de Serviço</a>
    </div>
    <div id='navbar' class='navbar-collapse collapse'>
      <ul class='nav navbar-nav navbar-right'>
        <li><a href=os_ativa.php>Início</a></li>
        <li><a href=cadastrar_os.php>Designar Ordem de Serviço</a></li>
        <li><a href=tecnico_gerencia.php>Gerenciar Técnico</a></li>
        <li><a href=terceirizados.php>Cadastro de Equipe</a></li>
        <li><a href=comissao_equipe.php>Comissão de Equipes</a></li>
        <li><a href=alterar_perfil.php>Perfil</a></li>
      </ul>
    </div>
  </div>
</nav>

";


?>