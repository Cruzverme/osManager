<?php 
  include "../config/db.php";
  
  include "../classes/header.php";
  include "../config/db_oracle.php";
  
?>

<body>
  <?php 
  
    include "../classes/nav.php";
  
    if(isset($_SESSION['menssagem']) && !empty($_SESSION['menssagem']))
    {
        print "<script>bootbox.alert({
                                        message:\"{$_SESSION['menssagem']}\",
                                        size:'small',
                                        backdrop: true
                                      })</script>";
        unset( $_SESSION['menssagem'] );
    }
  
  ?>

  
  <div id="main" class="container-fluid">
    
    <div id="top" class="row">
      <div class="col-md-3">
        <h2>Ajustes de Comissão</h2>
      </div>
    </div>
  
    <div class="row">
      <div>
        <h2>Ajuste de Valor</h2>
      </div>
      <form action="../classes/alterarValorComissao.php" method="post">
        
        <div class="row">
          <div class="col-md-12">
            <div class="form-group col-md-6">
              <label for="campoOrdemServico">Ordem de Serviço</label>
              <input type="number" name="os" id="campoOrdemServico" placeholder="Insira a Ordem de Serviço" class="form-control">
            </div>
            
            <div class="form-group col-md-6">
              <label for="campoValorComissao">Valor da Comissão</label>
              <input type="number" min="0.00" step=any id="campoValorComissao" name="valor_comissao" placeholder="Insira a comissão" class="form-control">
            </div>
          </div>
          <div class="col-md-12">  
            <div class="col-md-4"></div>
            <div class="form-group col-md-4">
              <input  name="atualizar_comissao" class="form-control btn btn-primary"  type="submit" value="Alterar Valor">
            </div>
            <div class="col-md-4"></div>
          </div>
        </div>
        
      </form>
    </div>

    <div class="row">
      <div>
        <h2>Ajuste de Data</h2>
      </div>
      <form action="../classes/alterarDataExecucao.php" method="post">
        <div class="row">
          <div class="col-md-12"> 

            <div class="form-group col-md-6">
              <label for="campoOrdemServicoData">Ordem de Serviço</label>
              <input type="text" name="os" id="campoOrdemServicoData" placeholder="Insira a Ordem de Serviço" class="form-control">
            </div>

            <div class="col-md-6 form-group date">
              <label for="dataCalendario" > Selecione o dia correto da Execução</label>
              <input type="text" class="form-control" id="dataCalendario" name="data"/>
            </div>

          </div>

          <div class="col-md-12">  
            <div class="col-md-4"></div>
            <div class="form-group col-md-4">
              <input  name="atualizar_comissao" class="form-control btn btn-primary"  type="submit" value="Ajustar Data">
            </div>
            <div class="col-md-4"></div>
          </div>
        </div>
      </form>
    </div>
    
    </div>
  </div>
</body>

<?php include "../classes/footer.php";?>