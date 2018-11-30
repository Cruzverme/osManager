jQuery(document).ready(function(){
  // Show password Button
	$("#showpassword").on('click', function(){
		var pass = $("#password");
		var fieldtype = pass.attr('type');
    
    if (fieldtype == 'password') {
			pass.attr('type', 'text');
			$(this).text("Hide Password");
		}else{
			pass.attr('type', 'password');
			$(this).text("Show Password");
		}
	});
});

$(document).ready(function() {
	var tabela = $('#tabelaOS').DataTable({
		order: [[12,'asc'],[10,'asc']],
		language: {
								search: 'Localizar',
								emptyTable: 'Tabela Vazia',
								lengthMenu: 'Mostrando _MENU_ itens',
								info: 'Exibindo do item _START_ até _END_ de um total _TOTAL_ resultados',
								infoFiltered: '(Filtrado de _MAX_ resultados)',
								infoEmpty: 'Exibindo do item 0 até 0 de um total 0 resultados' ,
								paginate:{
									first: 'Primeiro',
									last: 'Ultima',
									next: 'Proximo',
									previous: 'Anterior'
								}

							}
	});
	//table.column(1).search('Developer').draw()
  //Event Listener for custom radio buttons to filter datatable
	$('.customRadioButton').change(function () 
	{
		//table.columns().search(this.value).draw();
		tabela.column(12).search(this.value).draw();
		console.log(this.value)
	});
});

$(document).ready(function(){
	var data = $('#dataCalendario');
	var options={
		format: 'dd/mm/yyyy',
		orientation: 'top left',
		todayHighlight: true,
		clearDates: 'clearDates',
		autoclose: true,
		clearBtn: true
	};
	data.datepicker(options);
});

$('.input-daterange').each(function() {
	var options={
		format: 'dd/mm/yyyy',
		orientation: 'top left',
		todayHighlight: true,
		clearDates: 'clearDates',
		autoclose: true,
		clearBtn: true
	};
	$(this).datepicker(options);
});

// ########## TECNICOS #################

function removerTecnico(id_usuario)
{
	var userID = id_usuario;
	bootbox.confirm({
		message: "Deseja Mesmo Excluir?",
		buttons: {
			confirm: {
					label: 'Sim',
					className: 'btn btn-success'
			},
			cancel: {
					label: 'Não',
					className: 'btn btn-danger'
			}
		},
		callback: function (result) {
			if(result)
				$.post("../classes/deletarTecnico.php",{id_usuario: userID},function(msg_retorno){
					bootbox.alert({
						title: "Remoção de Técnico",
						message: msg_retorno,
						callback: function()
						{
							location.reload(true);
						}
					});
				});
			else{
				bootbox.alert({
					title: "Remoção de Técnico",
					message: "Operação Cancelada!",
				});
			}
		}
	});
}

function alterarSenha(id_usuario)
{
	var userID = id_usuario;
	var modal = bootbox.dialog({
		message: $(".form-content").html(),
		title: "Alterar Senha",
		buttons:[
			{
				label: "Alterar",
				className: "btn btn-primary pull-left",
				callback: function()
				{
					var form = modal.find(".form");
					var items = form.serialize();
					
					$.post("../classes/alterarSenha.php",{novaSenha: items, id_usuario: userID},function(msg_retorno){
					 bootbox.alert({
					 	title: "Alteração de Senha",
					  		message: msg_retorno,
					  		callback: function()
					  		{
					  			console.log(msg_retorno);
					  		}
					  	});
					});
				}
			},
			{
				label: "Fechar",
				className: "btn btn-default pull-left",
				callback: function() {
					console.log("Fechado");
				}
			}
		],
		show: false,
		onEscape: function() {
			modal.modal("hide")
		}
	});
	modal.modal("show");
}

// ########## OS #################

function alterarSituacaoOrdem(ordem,contrato,tecnico,tipo)
{
	var ordemServico = ordem;
	var contrato = contrato;
	
	console.log(tipo);
	bootbox.confirm({
		message: "Deseja Mesmo " + tipo + "?",
		buttons: {
			confirm: {
					label: 'Sim',
					className: 'btn btn-success'
			},
			cancel: {
					label: 'Não',
					className: 'btn btn-danger'
			}
		},
		callback: function (result) 
		{
			if(result)
			{
				$.post("../classes/alterarSituacao.php",{os: ordemServico,contrato: contrato, tec: tecnico, tipoSituacao: tipo},function(msg_retorno)
				{
					bootbox.alert({
						title: "Situação de OS",
						message: msg_retorno,
						callback: function()
						{
							location.reload(true);
						}
					});
				});
			}else{
				bootbox.alert({
					title: "Situação de OS",
					message: "Operação Cancelada!",
				});
			}
		}
	});
}

//######### DATA DE OS PARA DESIGNAR ###########

function definirDataOS()
{	
	var modal = bootbox.dialog({
		message: $(".form-content").html(),
		title: "Qual dia das OSs?",
		buttons:[
			{
				label: "Buscar",
				className: "btn btn-primary pull-left",
				callback: function()
				{
					var form = modal.find(".form");
					
					var items = form.serialize();
					console.log(items);
					
					$.get("../views/cadastrar_os.php",{calendario: items}).done(function(){	
						window.location.replace("../views/cadastrar_os.php?calendario="+items);
					}).fail(function(){
						alert('Ocorreu um Erro Desconhecido!!');
					});
				}
			},
			{
				label: "Fechar",
				className: "btn btn-default pull-left",
				callback: function() {
					console.log("Fechado");
				}
			}
		],
		show: false,
		onEscape: function() {
			modal.modal("hide")
		}
	});
	modal.modal("show");
}

// ########## USUARIO #################

function removerUsuario(id_usuario)
{
	var userID = id_usuario;
	bootbox.confirm({
		message: "Deseja Mesmo Excluir?",
		buttons: {
			confirm: {
					label: 'Sim',
					className: 'btn btn-success'
			},
			cancel: {
					label: 'Não',
					className: 'btn btn-danger'
			}
		},
		callback: function (result) {
			if(result)
				$.post("../classesUsuario/deletarUsuario.php",{id_usuario: userID},function(msg_retorno){
					bootbox.alert({
						title: "Remoção de Usuário",
						message: msg_retorno,
						callback: function()
						{
							location.reload(true);
						}
					});
				});
			else{
				bootbox.alert({
					title: "Remoção de Usuário",
					message: "Operação Cancelada!",
				});
			}
		}
	});
}

function alterarSenhaUsuario(id_usuario)
{
	var userID = id_usuario;
	var modal = bootbox.dialog({
		message: $(".form-content").html(),
		title: "Alterar Senha",
		buttons:[
			{
				label: "Alterar",
				className: "btn btn-primary pull-left",
				callback: function()
				{
					var form = modal.find(".form");
					var items = form.serialize();
					$.post("../classesUsuario/alterarSenhaUsuario.php",{parametros: items, id_usuario: userID},function(msg_retorno){
					 bootbox.alert({
					 	title: "Alteração de Usuário",
					  		message: msg_retorno,
					  		callback: function()
					  		{
					  			location.reload(true);
					  		}
					  	});
					});
				}
			},
			{
				label: "Fechar",
				className: "btn btn-default pull-left",
				callback: function() {
					console.log("Fechado");
				}
			}
		],
		show: false,
		onEscape: function() {
			modal.modal("hide")
		}
	});
	modal.modal("show");
}

//## AJUSTE DE COMISSAO ###
function ajustarValorComissao(ordemServico)
{
  var ordem = ordemServico;
  var modal = bootbox.dialog({
    message: $(".form-content").html(),
    title: "Ajustar Valor da Ordem ",
    buttons:[
      {
        label: "Ajustar",
        className: "btn btn-primary pull-left",
        callback: function()
        {
          var form = modal.find(".form");
          var items = form.serialize();
          $.post("../classes/ajustarValorComissao.php",{valor_comissao: items, os: ordem},function(msg_retorno){
              bootbox.alert({
                title: "Ajuste de Comissão",
                message: msg_retorno,
                callback: function()
                {
                  location.reload(true);
                }
              });
          });
        }
      },
      {
        label: "Fechar",
        className: "btn btn-default pull-left",
        callback: function() {
          console.log("Fechado");
        }
      }
    ],
    show: false,
    onEscape: function() {
      modal.modal("hide")
    }
  });
  modal.modal("show");
}


//#### EQUIPE TECNICA####

function removerEquipe(id_equipe)
{
	var equipeID = id_equipe;
	bootbox.confirm({
		message: "Deseja Mesmo Excluir?",
		buttons: {
			confirm: {
					label: 'Sim',
					className: 'btn btn-success'
			},
			cancel: {
					label: 'Não',
					className: 'btn btn-danger'
			}
		},
		callback: function (result) {
			if(result)
				$.post("../classes/deletarEquipe.php",{id_equipe: equipeID},function(msg_retorno){
					bootbox.alert({
						title: "Remoção de Equipe",
						message: msg_retorno,
						callback: function()
						{
							location.reload(true);
						}
					});
				});
			else{
				bootbox.alert({
					title: "Remoção de Equipe",
					message: "Operação Cancelada!",
				});
			}
		}
	});
}

function alterarNomeEquipe(id_equipe)
{
	var equipeID = id_equipe;
	var modal = bootbox.dialog({
		message: $(".form-content").html(),
		title: "Alterar Nome",
		buttons:[
			{
				label: "Alterar",
				className: "btn btn-primary pull-left",
				callback: function()
				{
					var form = modal.find(".form");
					var items = form.serialize();
					
					$.post("../classes/alterarNomeEquipe.php",{novoNome: items, id_equipe: equipeID},function(msg_retorno){
					 	bootbox.alert({
					 		title: "Alteração de Nome",
					  		message: msg_retorno,
					  		callback: function()
					  		{
									console.log(msg_retorno);
									location.reload(true);
					  		}
						});
					});
				}
			},
			{
				label: "Fechar",
				className: "btn btn-default pull-left",
				callback: function() {
					console.log("Fechado");
				}
			}
		],
		show: false,
		onEscape: function() {
			modal.modal("hide")
		}
	});
	modal.modal("show");
}

//######## INSERE LINHA TECNICO CONSULTAR DIARIO #########
$("#seletor_tecnico_os").change(function(){
	var nomeTecnico = $(this).val();
	var listaInserida = $("ul .list-group");
	var listaVal = $(".listaDoTecnico");
	listaVal.remove();
	
	$.post("../classes/consultarOSTecnico.php",{nome: nomeTecnico},function(msg_retorno){
		listaInserida.append(msg_retorno);
	});
});