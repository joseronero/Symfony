
/** Modal para conirmar borrado del usuario desde la vista ver usuario**/  
function borrarUsuario(){
      $('.ui.small.modal').modal({
          closable:false,
          transition:'fade up',
          onApprove: function(){
              $("form[name='form']").submit();
          }
      }).modal('show');
  };  
  
  
/** Modal para confirmarción desde los listados de tareas y usuarios**/  
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
   
   
 $('a[id="bandera_es"]').click (function(event){
     event.preventDefault();
     $('#idiomaVal').val('es');
     var idioma = $('#idiomaVal').val();
     console.log(idioma);
     $('#idiomaForm').submit();
 }); 
 
  $('a[id="bandera_en"]').click (function(event){
     event.preventDefault();
     $('#idiomaVal').val('en');
      var idioma = $('#idiomaVal').val();
     console.log(idioma);
     $('#idiomaForm').submit();
 });  