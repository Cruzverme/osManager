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

