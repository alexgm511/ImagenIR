// JavaScript Document
$(document).ready(function() {
	if ($('.hideType').css('display') == 'block') {
		$('input#formType').attr('value', 2);
	}
    $(".topNavegation").find("#contacto").click(function() {
		
        alert("Contact info");
    });

});