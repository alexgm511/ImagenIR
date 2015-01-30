// JavaScript Document
					  
$(document).ready(function() {
	// Underscore template for user table
	var usrTable = "<% _.each(allUsuarios, function(item) { %>" + 
                      "<tr><td><%= item.usuarioID %></td>" +
                      "<td><%= item.nombre %></td>" +
                      "<td><%= item.apellidoP %></td>" +
                      "<td><%= item.eMail %></td>" +
                      "<td><%= item.nivel %></td>" +
                      "<td><%= item.Images %></td>" +
                      "<td>&nbsp;</td>" +
					  "<td class='editData'><span class='glyphicon glyphicon-pencil'></span></td>" +
					  "<td class='delData'><span class='glyphicon glyphicon-remove'></span></td>" +
					  "<tr>" +
                      "<% }); %>";
	
	$('.panelCliente button.btnVolver').click(function() {
		window.location.href = "img1-5-0.php";
    });
	
	// Display the tblUsers panel with the user list - Close usrInput form 
	$('.adminTasks button#usrList').click(function(e) {
		$('.panel#usrInput .panel-heading button.close').click();
        var result = _.template(usrTable, allUsuarios);
		$('.panel#tblUsrs .panel-heading strong').html('Usuarios de ImagenIR');
		$('.panel#tblUsrs tbody').html(result);
		$('.panel#tblUsrs').css('display', 'block');	
    });
	// Close tblUsers panel
	$('.panel#tblUsrs .panel-heading button.close').click(function(e) {
		$('.panel#usrInput form#imgAdmTasks input, select').not('[name="imgTask"],[name="usuarioID"]').each(function(index, element) {
			if ($(this).attr('name') == "eMail") {
				//console.log('found eMail');
				$(this).attr('readOnly', false);
			}
			$('.panel#usrInput input[name="eMail"]').attr('readOnly', false);
			$(this).val('');
			$(this).css('display', 'block');
		});	
        $('.panel#tblUsrs tbody').html('');
		$('.panel#tblUsrs').css('display', 'none');	
    });

	// Delete users
	$('.panel#tblUsrs .usrTable tbody').on('click', 'td.delData', function(e) {
		var delVerify = false;
		var myTr = $(this).closest('tr');
        var myID = $(this).closest('tr').find('td:nth-child(1)').text();
		var myImgs = $(this).closest('tr').find('td:nth-child(6)').text();
        var name = $(this).closest('tr').find('td:nth-child(2)').text() + " " + $(this).closest('tr').find('td:nth-child(3)').text();
		//console.log("Clicked, my Id = " + myID + ", and I have " + myImgs + " images.");
		$('#myModal #myModalLabel').html('Eliminar Usuario');
		
		if (myImgs > 0) {
			var noDel = "Este usuario tiene " + myImgs + " imagenes, no se puede eliminar.";
			$('#myModal .modal-body').html(noDel);
			$('#myModal .modal-footer button#modConfirm').css('display', 'none');
			$('#myModal').modal('show');
		} else {
			var delMsg = "El usuario " + name + " va a ser eliminado. Seguro?";
			//console.log("Clicked, my Id = " + myID + ", and I have " + myImgs + " images.");
			$('#myModal .modal-body').html(delMsg);
			$('#myModal .modal-footer button#modConfirm').css('display', 'inline-block');
			$('#myModal').modal('show');
			$('#myModal .modal-footer button#modConfirm').click(function(e) {
				delUser(myTr, myID);
				$('#myModal').modal('hide');
			});
		}
    }); 

	// Edit users
	$('.panel#tblUsrs .usrTable tbody').on('click', 'td.editData', function(e) {
		var myTr = $(this).closest('tr');
        var myID = parseInt(myTr.find('td:nth-child(1)').text());
		var nombre = '';
		var apellidoP = '';
		var apellidoM = '';
		var eMail = '';
		var nivel = '';
		var ursInfo = _.where(allUsuarios, {"usuarioID" : myID});
		_.each(ursInfo, function(elem, indx, list) {
			nombre = elem.nombre;
			apellidoP = elem.apellidoP;
			apellidoM = elem.apellidoM;
			eMail = elem.nombre;
			nivel = elem.nivel;
		});
		//console.log(nombre + ' ' + apellidoP + ' ' + apellidoM + ' ' + eMail + ' ' + nivel);
		var chgMsg = 'Cambio al usuario ' + nombre + ' ' + apellidoP;
		var frmBtns = '<button class="btn btn-default btn-block" id="chgPass">Cambiar contrase&ntilde;a</button><button class="btn btn-default btn-block" id="edUser">Editar nombre, mail o nivel</button>';
		$('#myModal #myModalLabel').html('Editar Usuario');
		$('#myModal .modal-body').html(chgMsg+frmBtns);
		$('#myModal .modal-footer button#modConfirm').css('display', 'none');
		$('#myModal').modal('show');
		// Password change code
		$('#myModal .modal-body').on('click', 'button#chgPass', function(e) {
			$('.panel#tblUsrs .panel-heading button.close').click(); // close user list
			$('.panel#usrInput .panel-heading strong').html(chgMsg); // new heading
			$('.panel#usrInput form#imgAdmTasks input, select').not('[name="clave"],[name="eMail"],[name="imgTask"],[name="usuarioID"]').each(function(index, element) {
                $(this).css('display', 'none');
            });
			$('.panel#usrInput form#imgAdmTasks').attr('action','_includes/imgAdminTasks.php');
			$('.panel#usrInput input:hidden[name="imgTask"]').attr('value','chgPwd');
			$('.panel#usrInput input[name="usuarioID"]').val(myID);
			$('.panel#usrInput input[name="eMail"]').val(eMail);
			$('.panel#usrInput input[name="eMail"]').attr('readOnly', true);			
			$('.panel#usrInput').css('display', 'block');	
			$('#myModal').modal('hide');
		});
		// Name and level change code
		$('#myModal .modal-body').on('click', 'button#edUser', function(e) {
			$('.panel#tblUsrs .panel-heading button.close').click(); // close user list
			$('.panel#usrInput .panel-heading strong').html(chgMsg); // new heading
			$('.panel#usrInput form#imgAdmTasks input, select').not('[name="nombre"],[name="apellidoP"],[name="apellidoM"],[name="nivel"],[name="imgTask"],[name="eMail"],[name="usuarioID"]').each(function(index, element) {
                $(this).css('display', 'none');
            });
			$('.panel#usrInput form#imgAdmTasks').attr('action','_includes/imgAdminTasks.php');
			$('.panel#usrInput input:hidden[name="imgTask"]').attr('value','editName');
			$('.panel#usrInput input[name="usuarioID"]').val(myID);
			$('.panel#usrInput input[name="nombre"]').val(nombre);
			$('.panel#usrInput input[name="apellidoP"]').val(apellidoP);
			$('.panel#usrInput input[name="apellidoM"]').val(apellidoM);
			$('.panel#usrInput select[name="nivel"]').val(nivel);
			$('.panel#usrInput input[name="eMail"]').val(eMail);
			$('.panel#usrInput input[name="eMail"]').attr('readOnly', true);			
			$('.panel#usrInput').css('display', 'block');	
			$('#myModal').modal('hide');
		});
    }); 
	
	$('.panel#usrInput form#imgAdmTasks input, select').change(function(e) {
        var myField = $(this);
		var chgFld = [];
		chgFld.push(myField.attr('name'));
		//**************		
	   /* if (!_.contains(grades, 100))
			alert("Found a perfect final score!");*/
    });
	
	// Open new user form - Close tblUsers panel
	$('.adminTasks button#usrNew').click(function(e) {
		$('.panel#tblUsrs .panel-heading button.close').click();
		// restore all entry fields
		$('.panel#usrInput form#imgAdmTasks input, select').not('[name="imgTask"],[name="usuarioID"]').each(function(index, element) {
                $(this).css('display', 'block');
		});
		//console.log($('.panel#usrInput #imgAdmTasks').attr('name'));
		$('.panel#usrInput .panel-heading strong').html('Usuario Nuevo');
		$('.panel#usrInput form#imgAdmTasks').attr('action','_includes/imgAdminTasks.php');
		$('.panel#usrInput input:hidden[name="imgTask"]').attr('value','newUser');
		$('#imgAdmTasks .msgs').html('');
		$('.panel#usrInput').css('display', 'block');	
    });
	// Close new user form
	$('.panel#usrInput .panel-heading button.close').click(function(e) {
		$('.panel#usrInput').css('display', 'none');	
    });
	// Submit form to post new user
	$('.panel#usrInput form#imgAdmTasks').submit(function(e) {  
		var form = $(this);
		var myAct = $('input[name="imgTask"]').val();
		var newUsr = $('input[name="imgTask"]').val() == "newUser" ? true : false;
		var nombre = $('input[name="nombre"]').val();
		var apellidoP = $('input[name="apellidoP"]').val();
		var apellidoM = $('input[name="apellidoM"]').val();
		var eMail = $('input[name="eMail"]').val();
		var nivel = $('select[name="nivel"]').val();
		console.log(form.serialize());
		console.log(nombre + " " + apellidoP + " " + eMail + " " + nivel);
		$.ajax({
		   type: "POST",
		   url: form.attr('action'),
		   data: form.serialize(),
		   success: function(response) {
				$('#imgAdmTasks .msgs').html(response);
				// add new user to page variable allUsuarios
					if (newUsr) {
						var newID = $('#imgAdmTasks .msgs div.alert').data("newid");
						allUsuarios.push( { "usuarioID":newID ,"nombre":nombre, "apellidoP":apellidoP,"apellidoM":apellidoM,"eMail":eMail,"nivel":nivel,"Images":0 } );
					} else {
						if (myAct == "editName") {
							console.log('one ajax for all');
						}
					}
                    form.each(function() {
                        this.reset();
                    });
		   },
		   error: function(response) {
			   $('#imgAdmTasks .msgs').html(response);
				//alert("Hubo un error" + response); // show response from the php script.
		   }
		 });
		e.preventDefault(); // avoid to execute the actual submit of the form.
    }); 

});

	// Delete user
	function delUser(myTr, myID) {
		console.log("ajax away!");
		$.ajax({
		   type: "POST",
		   url: "_includes/imgAdminTasks.php",
		   data: {
			"imgTask" : "delUser",
			"usuarioID" : myID
		},
		   success: function(response) {
				$('#tblUsrs .msgs').html(response);
				// delete new user from page variable allUsuarios
				allUsuarios = _.reject(allUsuarios, function(users){ return users.usuarioID == myID; });
				myTr.remove();
				console.log(JSON.stringify(allUsuarios));
		   },
		   error: function(response) {
			   $('#tblUsrs .msgs').html(response);
		   }
		 }); 
	}

	// Change password
	function chgPass(frmInfo) {
		console.log("ajax away!");
		$.ajax({
		   type: "POST",
		   url: "_includes/imgAdminTasks.php",
		   data: frmInfo,
		   success: function(response) {
				$('#tblUsrs .msgs').html(response);
		   },
		   error: function(response) {
			   $('#tblUsrs .msgs').html(response);
		   }
		 }); 
	}
