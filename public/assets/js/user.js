$(function(e){


    $(".eliminarUsuario").on('click',function(e){
        e.preventDefault();
        $('#infoUsuario').html($(this).data('infousuario'));
        $('a#btnEliminar').data('id',$(this).data('id'));
        $('a#btnEliminar').attr('href', 'eliminaUser.html' + '?'+'id='+$(this).data('id'));
        $('#modalEliminaUsuario').modal('show');
    });

    $(".sanciona-usuario").on('click',function(e){
        
        e.preventDefault();
        e.stopPropagation();

        //set default values
        $( "#datepickerFin" ).datepicker( "setDate", new Date() );
        $('div#modal-sanciona-usuario span#nombre').html( $(this).data('nombre'));
        $('div#modal-sanciona-usuario span#correo').html( $(this).data('correo'));
        
        $('div#modal-sanciona-usuario div#enviar-correo').fadeOut('3000');
        if ( $(this).data('correo') != '' ) {
            
            $('div#modal-sanciona-usuario div#enviar-correo').fadeIn('3000');
            $('div#modal-sanciona-usuario #envia-correo').attr('checked', true);
        }
        
        


        //$('a#btnEliminar').data('id',$(this).data('id'));
        //$('a#btnEliminar').attr('href', 'eliminaUser.html' + '?'+'id='+$(this).data('id'));
        
        $('#modal-sanciona-usuario').modal('show');
    });

    $("#addUser").on('click',function(e){
		e.preventDefault();
		$('#modalAddUser').modal('show');
    });
   
    //Lanza ajax function para salvar nuevo usuario
    $('#btnSalvarRecurso').on('click',function(e){
        e.preventDefault();
        $data = $('form#nuevoUsuario').serialize();
        $.ajax({
            type: "POST",
            url: "salvarNuevoUsuario",
            data: $data,
            success: function($respuesta){
                if ($respuesta['error'] == false){
                    $('#modalAddUser').modal('hide');
                    location.reload();
                }
                //Hay errores de validación del formulario
                else {
                   //console.log($respuesta['errors']);
                   //reset
                   $('.has-error').removeClass('has-error');//borrar errores anteriores
                   $('.spanerror').each(function(){$(this).slideUp();});
                   //new errors
                   $.each($respuesta['errors'],function(key,value){
                        $('#fg'+key).addClass('has-error');
                        $('#'+key+'_error > span#text_error').html(value);
                        $('#'+key+'_error').fadeIn("slow");
                        $('#'+key+'_error').fadeIn("slow");

                        $('#aviso').slideDown("slow");
                        
                    });     
                }
                },
                error: function(xhr, ajaxOptions, thrownError){
                        hideGifEspera();
                        alert(xhr.responseText + ' (codeError: ' + xhr.status +')');
                    }
                });
    }); 

   function showGifEspera(){
		
		$('#espera').css('z-index','1000');
	}

	function hideGifEspera(){
		$('#espera').css('z-index','-1000');
	}

});