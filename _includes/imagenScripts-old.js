// JavaScript Document
var panelWidth = 0;
var startPanel = 1;
var currImg = 0;
var imgAnalizar = false;

var curY = 0;
var curX = 0;
var imgTemp = 0;
var imgOrigin = 0;
var pgPosX = 0;
var pgPosY = 0;
var whlFile = [];
var eachLine = [];
var imgMatrix = [];
var imgMatrixID = 0;

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
		$('.imgDataBlock .imgData').detach();
		$('.imgDataBlock').empty();
		//$('.tl .tabs span#imgTabA').css('display', 'none');
		$('.tl .tabs span#imgTabA').removeClass().text('').css('display', 'none');
		$('.ic .panels .panel1#imgA').css('display', 'none');
		$(".tl .tabs span#imagenes").trigger('click');
	});
	
	$('.panel1').on('click', '.analizar span', function() {
		if (window.imgMatrixID != window.currImg) {
			window.imgMatrix.length = 0;
		}
		$.ajax({
			url: "imgData.php?imgID="+window.currImg,
			cache: false}).done(function( html ) {
				// take incoming text file and store into array
				if (html == "Empty" || html == "NoFile") {
					window.imgAnalizar = false;
					window.imgMatrix.length = 0;
				} else {
				window.whlFile = html.split('\n');
				for (var i=0; i<120; i++) {
					window.eachLine = window.whlFile[i];
					window.imgMatrix[i] = window.eachLine.split(',');
				}
				window.imgMatrixID = window.currImg;
				window.imgAnalizar = true;
				}
			//$(".imgDataBlock").append(html);
		});
		if ($('.imgDataBlock .imgData.'+window.currImg)) {
			$('.analizar span').css('display', 'none');
			$('.rtSide2 .showTemp').css('display', 'block');
			window.imgAnalizar = true;
		}
    });
	
	//if (imgAnalizar) {
	$('.lfSide2 img').hover(function(e) {
		if(!imgAnalizar){
			return false;
		}
	}).mousemove(function(e){
		if (imgAnalizar) {
	   window.imgOrigin = $('.ic').position();
	   window.curX = (Math.round((e.pageX-window.imgOrigin.left-24)/4));
	   window.curY = (Math.round((e.pageY-window.imgOrigin.top-10)/4));
	   window.imgTemp = window.imgMatrix[window.curY][window.curX];
	   $('.rtSide2 .showTemp').html(window.imgTemp+'&deg;');   
		}
	}); 
	//}

//	$('.lfSide2').on('hover','img', function() {
//	}).mousemove(function(e){
//	   var imgOrigin = $('.ic').position();
//	   var curX = (Math.round((e.pageX-imgOrigin.left-25)/4));
//	   var curY = (Math.round((e.pageY-imgOrigin.top-10)/4));
//	   var imgTemp = $('.imgDataBlock .imgData.'+window.currImg+' .imgDataRow.'+curY).text().slice((curX*6),((curX*6)+6));
//	   $('.rtSide2 .showTemp').html(imgTemp+'&deg;');        
//	}); 
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
	}

	function displayImagen(imgID) {
		var imgNewIndex = 2; 
		var imgPosition = 1 * window.panelWidth;

		var imgName = $('.imgInfoPanel.'+imgID+' .imgInfoHed h3').text();
		if ($(".tl .tabs span."+imgID).length > 0) {
			$(".tl .tabs span."+imgID).trigger('click');
		} else {
			// clear info if there was another image before
			//$('.imgDataBlock').empty();
			$('.imgDataBlock .imgData').detach();
			$('.analizar span').css('display', 'inline-block');
			$('.rtSide2 .showTemp').css('display', 'none');
			
			$('.tl .tabs span#imgTabA').css('display', 'inline-block');
			$('.tl .tabs span').removeClass('selected');
			$('.tl .tabs span#imgTabA').attr({class: imgID+' selected'});
			$('.tl .tabs span#imgTabA').text(imgName);
			
			// use thumbnail URL minus "_tn" for big image URL
			// assumes all thumbnail images follow same pattern
			var imgURL = $('.lfSide1 .tnImage img#'+imgID).attr('src');
			imgURL = imgURL.replace("_tn.jpg", ".jpg"); 
			var imgShow = "<img class='"+imgID+"' src='"+imgURL+"' width='640' height='480' /><br/>";
			var imgInfoHed = $('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoHed').html();
			var imgInfoBlock = $('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoBlock').html();
			
			
			$('.ic .panels #imgA').attr({class: 'panel1 '+ imgID});
			$('.ic .panels .panel1#imgA .lfSide2 img').attr({id: imgID, src: imgURL});
			$('.ic .panels .panel1#imgA').css('display', 'block');
			//$('.ic .panels .panel1#imgA .lfSide2').html(imgShow);
			//$('.ic .panels .panel1#imgA .rtSide2').html(imgInfo+"<div class='analizar'><span>Analizar</span></div>")
			$('.ic .panels .panel1#imgA .rtSide2 .imgInfoHed').html(imgInfoHed);
			$('.ic .panels .panel1#imgA .rtSide2 .imgInfoBlock').html(imgInfoBlock);
			window.currImg = imgID;
			$(".tl .tabs span."+imgID).trigger('click');
		}
	}
