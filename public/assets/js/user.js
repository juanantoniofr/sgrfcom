$(function(e){


    $(".eliminarUsuario").on('click',function(e){
        e.preventDefault();
        e.stopPropagation();
        $('#infoUsuario').html($(this).data('infousuario'));
        $('a#btnEliminar').data('id',$(this).data('id'));
        $('a#btnEliminar').attr('href', 'eliminaUser.html' + '?'+'id='+$(this).data('id'));
        $('#modalEliminaUsuario').modal('show');
    });

    $(".eliminaSancion").on('click',function(e){
        e.preventDefault();
        e.stopPropagation();
                
        $('div#modalEliminaSancion span#infouser').html( $(this).data('nombre') );
        $('div#modalEliminaSancion span#infosancion').html( '<p><b>Motivo:</b><br />' + $(this).data('motivosancion') + '</p><p><b>Fin sanción:</b> ' + $(this).data('ffinsancion') + '</p>');
        
        $('a#btnEliminaSancion').attr('href', 'eliminaSancion' + '?'+'idUser='+$(this).data('id') + '&'+'idSancion='+$(this).data('idsancion'));
        $('#modalEliminaSancion').modal('show');
    });

    $(".sanciona-usuario").on('click',function(e){
        
        e.preventDefault();
        e.stopPropagation();

        //set default values
        $('div#modal-sanciona-usuario div#msg').fadeOut('100');
        $('div#modal-sanciona-usuario span#nombre').html( $(this).data('nombre'));
        $('div#modal-sanciona-usuario span#correo').html( $(this).data('correo'));
        $('div#modal-sanciona-usuario #motivo-sancion').html( $(this).data('motivosancion') );
        
        var $date = new Date();
        if ( $(this).data('ffinsancion') != '' ) $date = $(this).data('ffinsancion');
        $( "#datepickerFin" ).val( $date );

        $('div#modal-sanciona-usuario div#enviar-correo').fadeOut('3000');
        if ( $(this).data('correo') != '' ) {
            
            $('div#modal-sanciona-usuario div#enviar-correo').fadeIn('3000');
            $('div#modal-sanciona-usuario #envia-correo').attr('checked', true);
        }
        
        $('div#modal-sanciona-usuario input[name="userId"]').val( $(this).data('id') );      
        $('#modal-sanciona-usuario').modal('show');
    });

    $('div#modal-sanciona-usuario #salvaSancion').on('click',function(e){

        e.preventDefault();
        e.stopPropagation();
        
        showGifEspera();
        $.ajax({
            type: "GET",
            url: "ajaxSancionaUsuario", /* terminar en controllador */
            data: {userId:$('div#modal-sanciona-usuario #userId').val(),motivoSancion:$.trim($('div#modal-sanciona-usuario #motivo-sancion').val()),f_fin:$('div#modal-sanciona-usuario input[name="f_fin"]').val()},
            success: function($respuesta){
                
                //console.log($respuesta);

                if ( $respuesta['exito'] == false ){
                    
                    $('div#modal-sanciona-usuario ul#list-errors').html($respuesta['msg']);
                    $('div#modal-sanciona-usuario div#msg').fadeIn('3500');
                }
                else{
                    $('#modal-sanciona-usuario').modal('hide');
                    location.reload();
                }
                    
            },
            error: function(xhr, ajaxOptions, thrownError){
                    hideGifEspera();
                    alert(xhr.responseText + ' (codeError: ' + xhr.status) +')';
            }
        });
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