$(function()
{ 
  
  $('body').on('click', '.ordensservicolista .list-group-item', function () {
    
    active = '';
    active = $('ul li.active');

    var key = Object.keys(active);
    var len = key.length -2;

    if(len == 0)
      $(this).toggleClass('active');
    
    else
      $(this).removeClass('active')
  });
  
  var quantidadeOS = 0;
  $('.list-arrows button').click(function () {
    var $button = $(this), actives = '';
    if ($button.hasClass('move-left')) {
      
      actives = $('.list-right ul li.active');
      var $lista =$('.list-left ul');
      $boxINPUT = $('.list-right ul li.active #listaOSEscondido ');
      valor = $boxINPUT.attr('name');

      actives.removeClass('active');

      if(valor == 'os_diaria[]')
      {
        if(actives.val() != $boxINPUT.val())
        {
          $boxINPUT.remove();
          actives.remove();
          $lista.append('<li class="list-group-item" value='+$boxINPUT.val()+'><input type=hidden id=listaOSEscondido name="" value='+$boxINPUT.val()+'>'+$boxINPUT.val()+'</li>');
          $lista = '';
          quantidadeOS-=1;
          var totalDiario = parseInt($("#quantidadeOSTotalDiaria").text());
          
          quantidadeTotal = totalDiario + 1;
          
          $("#quantidadeOSTotalDiaria").text(quantidadeTotal);
          $("#quantidadeOSDiaria").text(quantidadeOS);
        }
      }
    } else if ($button.hasClass('move-right')) {
      
      actives = $('.list-left ul li.active');
      var $lista =$('.list-right ul');
      $boxINPUT = $('.list-left ul li.active #listaOSEscondido ');
      valor = $boxINPUT.attr('name');

      actives.removeClass('active');
      
      if(valor == '')
      {
        if(actives.val() == $boxINPUT.val())
        {
          $boxINPUT.remove();
          actives.remove();
          $lista.append('<li class="list-group-item" val=""><input type=hidden id=listaOSEscondido name="os_diaria[]" value='+$boxINPUT.val()+'>'+$boxINPUT.val()+'</li>');
          $lista = '';
          quantidadeOS+=1
          var totalDiario = $("#quantidadeOSTotalDiaria").text();

          quantidadeTotal = totalDiario - 1;
          
          $("#quantidadeOSTotalDiaria").text(quantidadeTotal);
          $("#quantidadeOSDiaria").text(quantidadeOS);
        } 
      }      
    }
  });

  $('.dual-list .selector').click(function () {
    var $checkBox = $(this);
    if (!$checkBox.hasClass('selected')) {
      $checkBox.addClass('selected').closest('.well').find('ul li:not(.active)').addClass('active');
      $checkBox.children('i').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
    } else {
      $checkBox.removeClass('selected').closest('.well').find('ul li.active').removeClass('active');
      $checkBox.children('i').removeClass('glyphicon-check').addClass('glyphicon-unchecked');
    }
  });

  $('.dual-list .add').click(function(){
    var $boxINPUT=$('.novaOrdemServico');
    var $lista =$('.list-right ul');
    
    if($boxINPUT.val() != "")
    {
      $lista.append('<li class="list-group-item"><input type=hidden  name="os_diaria[]" value='+$boxINPUT.val()+'>'+$boxINPUT.val()+'</li>');
      $boxINPUT.val('');
      console.log("vou funfar");
    }
  });

  $('.dual-list .remover').click(function () {
    var $button = $(this), actives = '';
    if ($button.hasClass('remover')) {
      actives = $('.list-right ul li.active');
      actives.remove();
      var $lista = $('.dual-list .selector')
      if($lista.hasClass('selected'))
      {
        $lista.removeClass('selected').closest('.well').find('ul li.active').removeClass('active');
        $lista.children('i').removeClass('glyphicon-check').addClass('glyphicon-unchecked');
      }
    }
  });

  $('[name="osADD"]').keydown(function (e) {
    var code = e.keyCode || e.which;
    if(code == 13)
    {
      var $boxINPUT=$('.novaOrdemServico');
      var $lista =$('.list-right ul');

      if($boxINPUT.val() != "")
      {
        $lista.append('<li class="list-group-item"><input type=hidden  name="os_diaria[]" value='+$boxINPUT.val()+'>'+$boxINPUT.val()+'</li>');
        $boxINPUT.val('');
        console.log("adicionado");
      }
    }
  });

  $('[name="SearchDualList"]').keyup(function (e) {
    var code = e.keyCode || e.which;
    if (code == '9') return;
    if (code == '27') $(this).val(null);
    var $rows = $(this).closest('.dual-list').find('.list-group li');
    var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
    $rows.show().filter(function () {
        var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
        return !~text.indexOf(val);
    }).hide();
  });

});