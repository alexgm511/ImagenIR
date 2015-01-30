// JavaScript Document
$(document).ready(function() {
	if ($('.hideType').css('display') == 'block') {
		$('input#formType').attr('value', 2);
	}
    $('.topNav button.btnContacto').click(function() {
        alert("Aqui tendremos el celular de Ariel!");
    });
	$('.topNav button.btnVolver').click(function() {
		window.location.href = "img1-5-0.php";
    });

});