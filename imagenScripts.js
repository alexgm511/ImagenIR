// JavaScript Document
var panelWidth = 0;
var startPanel = 1;

$(document).ready(function() {
        $('.hedInfoBar .topBtn').click(function() {
            window.location = 'sign-up.php?errmsg=7';
        });
		
		window.panelWidth = $('.ic').width();

	$('.panel_container .panel').each(function(index){
        $(this).css({'width':window.panelWidth+'px','left':(index*window.panelWidth)+'px'});
		
		$('.ic .panels').css('width',(index+1)*window.panelWidth+'px');
    });
	
	$('.tl .tabs').on('click', 'span', function() {
		changePanels($(this).index());
    });

	$('.sp .tabs span:nth-child('+window.startPanel+')').trigger('click');

	// On Hover display imgInfoPanel table of image info
	$('.lfSide1 .tnImage img').hover(function() {
		var imgID = $(this).attr('id');
		$('.imgPanel').html(
			$('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoHed').html()+' <div class="imgInfoBlock">'+$('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoBlock').html()+'</div>'
		);},
		function() {
			$('.imgPanel').empty();
	});
	$('.lfSide1 .tnImage img').dblclick(function() {
		var imgID = $(this).attr('id');
		displayImagen(imgID);
	}); 
	$('.lfSide1 .tnImage a').click(function() {
		var imgID = $(this).attr('id');
		displayImagen(imgID);
	}); 
	// Remove the clicked image panel and corresponding tab
	$('.ic .panels').on('click', '.close img', function() {
        var imgID = $(this).attr('id');
		$('.tl .tabs span.'+imgID).remove();
		$(this).closest('div.panel1').remove();
		$(".tl .tabs span#imagenes").trigger('click');
	});
	
	$('.ic .panels').on('mouseover', '.lfSide2 img', function() {
	   var imgOrigin = $(this).position();
	   var imgID = $(this).attr('class');
	}).mousemove(function(e){
	   var curX = (Math.round((e.pageX-imgOrigin.left)/4));
	   var curY = (Math.round((e.pageY-imgOrigin.top)/4));
	   var imgTemp = $('.imgInfoPanel.'+imgID+' .imgData .imgDataRow.'+curY).text().slice((curX*6),((curX*6)+6));
	   $('.showTemp').html(imgTemp+'&deg;');        
	});
		
	window.showTmpo = function(e) {
		imgClass = e;
		//alert($('.lfSide2 img.'+e).attr('width'));
		$('.lfSide2 img.'+imgClass).mousemove(function(e){
	   var imgOrigin = $('.lfSide2 img.'+imgClass).position();
	   alert(imgOrigin.top+" "+imgOrigin.left);
	  // var imgID = $(this).attr('id');     
	   var curX = (Math.round((e.pageX-imgOrigin.left)/4));
	   var curY = (Math.round((e.pageY-imgOrigin.top)/4));
	   //var imgTempTest = $('.imgInfoPanel.'+imgClass+' .imgData .imgDataRow.'+curY).text();
		
	   var imgTemp = $('.imgInfoPanel.'+imgClass+' .imgData .imgDataRow.'+curY).text().slice((curX*6),((curX*6)+6));
	   $('.showTemp').html(imgTemp+'&deg;');        
	   }); 
	}
		
});
		// Move panel to clicked tab panel
		function changePanels(newIndex){
			var newPanelPosition = (window.panelWidth * newIndex)*-1;
			var newPanelHeigth = $('.ic .panel:nth-child('+(newIndex+1)+')').find('.panel_content').height() + 15;
			
			$('.ic .panels').css({ left:newPanelPosition+"px"});
			//$('.ic .panels').animate({left:newPanelPosition}, 1000);
			$('.ic .panel_container').animate({height:newPanelHeigth}, 1000);
			
			$('.tl .tabs span').removeClass('selected');
			$('.tl .tabs span:nth-child('+(newIndex+1)+')').addClass('selected');
			
		// Recalculate Panel width and Panel1 positions
		//  in case one or more have been removed.
		$('.panel_container .panels .panel1').each(function(index){
			$(this).css({'width':window.panelWidth+'px','left':(index*window.panelWidth)+'px'});
			
			$('.panel_container .panels').css('width',(index+1)*window.panelWidth+'px');
		});

		}

		function displayImagen(imgID) {
			var imgNewIndex = $('.tl .tabs').children().length+1; 
			var imgPosition = (imgNewIndex-1) * window.panelWidth;

			var imgName = $('.imgInfoPanel.'+imgID+' .imgInfoHed h3').text();
			if ($(".tl .tabs span#"+imgName).length > 0) {
				$(".tl .tabs span#"+imgName).trigger('click');
			} else {
				var newSpan = document.createElement('span');
				newSpan.id = imgName;
				$('.tl .tabs span').removeClass('selected');
				newSpan.className = imgID+" selected";
				newSpan.textContent = imgName;
				$(".tl .tabs").append(newSpan);
				
				// use thumbnail URL minus "_tn" for big image URL
				// assumes all thumbnail images follow same pattern
				var imgURL = $('.lfSide1 .tnImage img#'+imgID).attr('src');
				imgURL = imgURL.replace("_tn.jpg", ".jpg"); 
				var imgShow = "<img class='"+imgID+"' src='"+imgURL+"' width='640' height='480' onmouseover='showTmp("+imgID+")' /><br/>";
				var imgInfo = $('.imgPanels .imgInfoPanel.'+imgID).html();
				var imgClose = $('.imgPanels .imgInfoPanel.'+imgID).html();
				var newPanel = "<div class='panel1 "+imgID+"' style='left:"+imgPosition+"'><div class='close'><img src='_images/cerrar.png' alt='Cerrar imagen' id='"+imgID+"' /></div><div class='lfSide2'>"+imgShow+"</div><div class='rtSide2'>"+imgInfo+"<h2 class='showTemp'></h2></div>";
				$('.ic .panels').append(newPanel);
				$('.ic .panels .panel1.'+imgID).css({'width':window.panelWidth+'px','left':(imgPosition)+'px'});
				var panelHeight = $('.ic .panels .panel1.'+imgID).height() + 15;
				$('.ic .panels').css('width',(imgNewIndex)*window.panelWidth+'px');
				//$('.ic .panels').animate({left:(imgPosition*-1)}, 1000);
				//$('.ic .panels').animate({left:(imgPosition*-1)});
				$('.ic .panels').css({ left:(imgPosition*-1)+"px"});
				$('.ic .panel_container').css('height', panelHeight);
			}
		}
