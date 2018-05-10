
/** Modal para conirmar borrado del usuario desde la vista ver usuario*/  
function borrarUsuario(){
      $('.ui.small.modal').modal({
          closable:false,
          transition:'fade up',
          onApprove: function(){
              $("form[name='form']").submit();
          }
      }).modal('show');
  };  
  
  
/** Modal para conirmar borrado del usuario desde el listado*/  
$('a[id^="enlace_"]').click(function (event){
    var href = $(this).attr('href');
    event.preventDefault();
    $('.ui.small.modal').modal({
          closable:false,
          transition:'fade up',
          onApprove: function(){
            window.location=href;
          }
      }).modal('show');
    
    });
    

    