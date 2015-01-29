// JavaScript Document
var panelWidth = 0;
var startPanel = 1;
var currImg = 0;
var imgAnalizar = false;

var curY = 0;
var curX = 0;
var imgTemp = 0;
var imgOrigin = 0;
var imgWidth = 640;
var imgHeight = 480;
var pgPosX = 0;
var pgPosY = 0;
var whlFile = [];
var eachLine = [];
var imgMatrix = [];
var imgMatrixID = 0;
var intTempSpot = 0;
var intSpots = 0;
var postSpotsURL = "postSpots.php";

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
	
    $('.equipo').find('.accordion-toggle').click(function(){
      //Expand or collapse this panel
      $(this).next().slideToggle('fast');
      //Hide the other panels
      $(".accordion-content").not($(this).next()).slideUp('fast');
    });

	// On Hover display imgInfoPanel table of image info
/*	$('.lfSide1 #treeList').find('li[rel]').hover( function() {
		var imgID = $(this).attr('id').substring(2);
		alert('Hovering over img: "+imgID');
		$('.imgPanel').html(
			$('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoHed').html()+' <div class="imgInfoBlock">'+$('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoBlock').html()+'</div>'
		);}, 
		function() {
			$('.imgPanel').empty(); 
	}); */
	
	
	$('.lfSide1 #treeList').on('dblclick', 'li[rel]', function() {
		var imgID = $(this).attr('id').substring(2);
		//alert('clicked on '+imgID);
		displayImagen(imgID);
	}); 
	$('.lfSide1 .tnImage a').click(function() {
		var imgID = $(this).attr('id');
		displayImagen(imgID);
	}); 
	// Remove the clicked image panel and corresponding tab
	$('.ic .panels').on('click', '.close img', function() {
        var imgID = $(this).attr('id');
		//$('.imgDataBlock .imgData').detach();
		imgAnalizar = false;
		$('.analizar span#TempSpotPost').css('display', 'none');
		//$('.imgDataBlock').empty();
		$('.tl .tabs span#imgTabA').removeClass().text('').css('display', 'none');
		$('.ic .panels .panel1#imgA').css('display', 'none');
		$(".tl .tabs span#imagenes").trigger('click');
	});
	
	$('.panel1').on('click', '.analizar span#showTemp', function() {
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
			$('.analizar span#showTemp').css('display', 'none');
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
	   window.curY = (Math.round((e.pageY-window.imgOrigin.top-11)/4));
	   window.pgPosX = e.pageX;
	   window.pgPosY = e.pageY;
	   window.imgTemp = window.imgMatrix[window.curY][window.curX];
	   $('.rtSide2 .showTemp').html(window.imgTemp+'&deg;');   
		}
	}).click(function(e) {
		if(imgAnalizar){
			$('.analizar span#TempSpotPost').css('display', 'inline-block');
			window.intSpots++;
			window.intTempSpot++;
			var tag = "<div class='tempSpot' id='temp_"+window.intTempSpot+"' style='left:"+(window.pgPosX-window.imgOrigin.left-60)+"px; top: "+(window.pgPosY-window.imgOrigin.top-18)+"px;' data-pgPosX='"+(window.pgPosX-window.imgOrigin.left)+"' data-pgPosY='"+(window.pgPosY-window.imgOrigin.top)+"' data-temp='"+window.imgTemp+"'>"+window.imgTemp+"&deg; <span><img src='_images/x-close.png' width='12' height='12' alt='x' /></span></div>";
			$('.lfSide2').append(tag);
		}
	}); 
	
	//close action of tempSpot 
	$('.panel1').on('click', '.tempSpot span', function() {
        $(this).closest('div.tempSpot').remove();
		window.intSpots--;
		if (window.intSpots==0){
			$('.analizar span#TempSpotPost').css('display', 'none');
		} else {
			$('.analizar span#TempSpotPost').css('display', 'inline-block');
		}
    });
	
	//posting tempSpots to database
	$('.panel1').on('click', '.analizar span#TempSpotPost', function() {
		var intSpot = 1;
		var arSpots = [];
		var strSpots = "";
		var firstSpot = true;
		var imgID = $('.lfSide2 img').attr('id');
		$('.lfSide2 .tempSpot').each(function(index) {
			// assemble comma delimited list of info for each point 
			var strSpotDet = $('.lfSide2 img').attr('id')+","+(index+1)+","+$(this).attr('data-pgposx')+","+$(this).attr('data-pgposy')+","+$(this).attr('data-temp');
			var strSpot = "pt"+index+"="+strSpotDet;			
			if(!strSpots == ""){
				strSpots += "&";
			}
			strSpots += strSpot; // comcantenate points into one string
			// update page for current session
			if (firstSpot) {
				$('.imgDataBlock .pt'+imgID).remove();
				firstSpot = false;
			}
			var localSpot = "<div class='pt"+imgID+"'>"+strSpotDet+"</div>";
			$('.imgDataBlock').append(localSpot);
        });
		$.ajax({
		  //points are sent on GET URL
		  url: window.postSpotsURL+"?"+strSpots,
		  success: function() {
			  // hide post button when spots are posted
			  $('.analizar span#TempSpotPost').css('display', 'none');
				//alert('Puntos de temperatura guardados.');
			},
		  async:false  // this will make site wait for response from database.
		}).done(function( html ) {
			alert(html);
		});
    });
});

/*	// Called from the tree nodes
	function hoverImg(imgID, start) {
		if (start) {
			$('.imgPanel').html($('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoHed').html()+' <div class="imgInfoBlock">'+$('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoBlock').html()+'</div>');
		} else {
			$('.imgPanel').empty(); 
		}
	}
*/

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
			//$('.imgDataBlock .imgData').detach();
			$('.analizar span#TempSpotPost').css('display', 'none');
			$('.analizar span#showTemp').css('display', 'inline-block');
			imgAnalizar = false;
			window.intSpots = 0;
			window.imgMatrix.length = 0;
			$('.rtSide2 h2.showTemp').text("");
			$('.rtSide2 .showTemp').css('display', 'none');
			$('.lfSide2 .tempSpot').remove();
			
			$('.tl .tabs span#imgTabA').css('display', 'inline-block');
			$('.tl .tabs span').removeClass('selected');
			$('.tl .tabs span#imgTabA').attr('class', imgID+' selected');
			$('.tl .tabs span#imgTabA').text(imgName);
			
			// use thumbnail URL minus "_tn" for big image URL
			// assumes all thumbnail images follow same pattern
			console.log(imgID);
			var imgURL = $('.imgInfoPanel.'+imgID).find('img#'+imgID).attr('src');
			console.log(imgURL);
			imgURL = imgURL.replace("_tn.jpg", ".jpg"); 
			var imgShow = "<img class='"+imgID+"' src='"+imgURL+"' width='640' height='480' /><br/>";
			var imgInfoHed = $('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoHed').html();
			var imgInfoBlock = $('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoBlock').html();
			
			
			$('.ic .panels #imgA').attr('class', 'panel1 '+ imgID);
			$('.ic .panels .panel1#imgA .lfSide2 img').attr({id: imgID, src: imgURL});
			$('.ic .panels .panel1#imgA').css('display', 'block');
			//$('.ic .panels .panel1#imgA .lfSide2').html(imgShow);
			//$('.ic .panels .panel1#imgA .rtSide2').html(imgInfo+"<div class='analizar'><span>Analizar</span></div>")
			$('.ic .panels .panel1#imgA .rtSide2 .imgInfoHed').html(imgInfoHed);
			$('.ic .panels .panel1#imgA .rtSide2 .imgInfoHed .tnImage').hide();
			$('.ic .panels .panel1#imgA .rtSide2 .imgInfoBlock').html(imgInfoBlock);
			window.currImg = imgID;
			$('.imgDataBlock .pt'+imgID).each(function(index) {
				var arPts = [];
				arPts = $(this).text().split(",");
				var tag = "<div class='tempSpot' id='temp_"+arPts[1]+"' style='left:"+(arPts[2]-60)+"px; top: "+(arPts[3]-18)+"px;' data-pgPosX='"+(arPts[2])+"' data-pgPosY='"+(arPts[3])+"' data-temp='"+arPts[4]+"'>"+arPts[4]+"&deg; <span><img src='_images/x-close.png' width='12' height='12' alt='x' /></span></div>";
				$('.lfSide2').append(tag); 
            });
			$(".tl .tabs span."+imgID).trigger('click');
		}
	}
