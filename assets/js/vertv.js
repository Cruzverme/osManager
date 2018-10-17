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
		'order': [[12,'asc'],[10,'asc']]
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

