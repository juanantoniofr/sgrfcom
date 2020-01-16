$(function(e) {
    
    $('[data-toggle="tooltip"]').tooltip();
    
    $( "#datepickerIni" ).datepicker( $.datepicker.regional[ "es" ] );
	$( "#datepickerFin" ).datepicker( $.datepicker.regional[ "es" ] );
	

	$( '.titulo-acordeon').click(function(e){;
		e.preventDefault();
		e.stopPropagation();
		$( this ).next().toggle('slow');
	});

});

function showGifEspera(){
    $('#espera').css('display','inline').css('z-index','100');
}

function hideGifEspera(){
    $('#espera').css('display','none').css('z-index','-100');
}