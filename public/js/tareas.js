function borrarUsuario(){
      $('.ui.small.modal').modal({
          closable:false,
          onApprove: function(){
              $("form[name='form']").submit();
          }
      }).modal('show');
  };  
