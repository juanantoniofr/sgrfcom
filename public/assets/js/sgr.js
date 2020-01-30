$(function(e) {
    
    $('[data-toggle="tooltip"]').tooltip();
    
    $( "#datepickerIni" ).datepicker( $.datepicker.regional[ "es" ] );
	$( "#datepickerFin" ).datepicker( $.datepicker.regional[ "es" ] );
	

	$( '.titulo-acordeon').click(function(e){;
		e.preventDefault();
		e.stopPropagation();
		$( this ).next().toggle('slow');
	});


	 $( document ).ready( function(){
	
	 		//alert($('#msg-inicio-sesion').data('mostrar'));
	 		console.log($('#msg-inicio-sesion').data('mostrar'));
	 		
	 		if ( $('#msg-inicio-sesion').data('mostrar') == true ) {
	 			
	 			//mostar venta modal
	 			$('#modalMsg').modal('show');
	 			//Estabelcer a false
	 			$('#msg-inicio-sesion').data('mostrar',false);
	 		}
	 });

});

function showGifEspera(){
    $('#espera').css('display','inline').css('z-index','100');
}

function hideGifEspera(){
    $('#espera').css('display','none').css('z-index','-100');
}