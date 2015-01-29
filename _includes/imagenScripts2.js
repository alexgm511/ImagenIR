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

/*	$('.tl .tabs').on('click', 'span', function() {
		//alert("Clicked! "+$(this).attr('id'));
		var newIdx = $(this).attr('class') -1;
		changePanels(newIdx);	
    });
*/	
	$('.sp .tabs span:nth-child('+window.startPanel+')').trigger('click');

	// Display imgInfoPanel table of image info
	$('.lfSide1 .tnImage img').hover(function() {
		var imgID = $(this).attr('id');
		$('.imgPanel').html(
			$('.imgPanels .imgInfoPanel.'+imgID).html()
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
});

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
			var imgNewIndex = $('.tl .tabs').children().length+1; 
			var imgPosition = (imgNewIndex-1) * window.panelWidth;

			var imgName = $('.imgInfoPanel.'+imgID+' .imgInfoHed h3').text();
			if ($(".tl .tabs span#"+imgName).length > 0) {
				$(".tl .tabs span#"+imgName).trigger('click');
			} else {
				var newSpan = document.createElement('span');
				newSpan.id = imgName;
				$('.tl .tabs span').removeClass('selected');
				newSpan.className = imgNewIndex+" selected";
				newSpan.textContent = imgName;
				$(".tl .tabs").append(newSpan);
				
				// use thumbnail URL minus "_tn" for big image URL
				// assumes all thumbnail images follow same pattern
				var imgURL = $('.lfSide1 .tnImage img#'+imgID).attr('src');
				imgURL = imgURL.replace("_tn.jpg", ".jpg"); 
				var imgShow = "<img id='"+imgID+"' src='"+imgURL+"' width='640' height='480' /><br/>";
				var imgInfo = $('.imgPanels .imgInfoPanel.'+imgID).html();
				var newPanel = "<div class='panel1 "+imgNewIndex+"' style='left:"+imgPosition+"'><div class='lfSide2'>"+imgShow+"</div><div class='rfSide2'>"+imgInfo+"</div></div>";
				$('.ic .panels').append(newPanel);
				$('.ic .panels .panel1.'+imgNewIndex).css({'width':window.panelWidth+'px','left':(imgPosition)+'px'});
				var panelHeight = $('.ic .panels .panel1.'+imgNewIndex).height() + 15;
				$('.ic .panels').css('width',(imgNewIndex)*window.panelWidth+'px');
				//$('.ic .panels').animate({left:(imgPosition*-1)}, 1000);
				//$('.ic .panels').animate({left:(imgPosition*-1)});
				$('.ic .panels').css({ left:(imgPosition*-1)+"px"});
				$('.ic .panel_container').css('height', panelHeight);
			}
		}
