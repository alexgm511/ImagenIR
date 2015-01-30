// JavaScript Document
var getState = 0;
var panelWidth = 0;
var startPanel = 1;
var currImg = 0;
var imgAnalizar = false;
var imgBig = false;
var noSpot = true;
var chartItems = 0;

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
	$('#Imagenes .setState li').click(function(e) {
		var myNav = $(this);
		if (myNav.attr('class') == 'stateImg') {
			window.getState = 0;
			$('.chartDisplayTemplate .chartHead button.chartClose').click();
			$('.reportTemplate .reportHead button.reportClose').click();			
		} else if (myNav.attr('class') == 'stateGraf') {
			$('.imgDisplayTemplate .imgHead button.imgClose').click();
			window.getState = 1;
		}
		$('#Imagenes .setState li').removeClass('active');
        myNav.addClass('active');
		console.log('getState = '+window.getState)
    });
	// Image or chart state
	$('#Imagenes #treeList').on('dblclick', 'li[rel]', function() {
		var imgID = $(this).attr('id').substring(2);
		var equip = $(this).parent().closest('li').find('a:first').text();
		//alert('clicked on '+imgID);
		$('.imgDisplayTemplate .imgHead button.imgClose').click();
	    if (window.getState == 0) {	
			displayImagen(imgID, equip);
		} else if (window.getState == 1){
			//console.log('Double clicked! '+imgID);
			addImgInfo(imgID, equip);
		}
	}); 
	
	// Delete clicked table row
	$('.chartDisplayTemplate').on('click', '.imgTabla td.delData', function() {
		//console.log('clicked');
		$(this).closest('tr').remove();
	});
	
	// Remove the chart section and all its components.
	$('.imgDisplay .chartDisplayTemplate').on('click', '.chartHead button.chartClose', function(e) {
		// Empty every element of imgDisplayTemplate without removing
		$('.chartDisplayTemplate  .imgTabla tbody').html('');

		$('.chartDisplayTemplate .chartMain').css('display', 'none');
		$('.chartMain #chartBlock').html('');
		//$('.chartMain #chartBlock').css('min-height', '0px');
	});
	
	// Remove the report section and all its components.
	$('.imgDisplay .reportTemplate').on('click', '.reportHead button.reportClose', function(e) {
		// Empty every element without removing
		$('.reportTemplate .reportHead h4').text('');
		$('.reportTemplate .reportBody #reportImgDC').html('');
		$('.reportTemplate .reportBody #repChartBlock').html('');
		$('.reportTemplate .reportTabla tbody').html('');
		$('.reportTemplate').css('display', 'none');		
	});
	
/*	// Print the generated report
	$('.imgDisplay .reportTemplate').on('click', '.reportHead button.reportPrint', function(e) {
	    var printContents = $('.reportTemplate').clone();
		var header = "<link type='text/css' rel='stylesheet' href='_css/img1.css'><script type='text/javascript' src='_includes/jquery-1.9.0.min.js'><script type='text/javascript' src='bootstrap/js/bootstrap.min.js'></script><link type='text/css' rel='stylesheet' href='bootstrap/css/bootstrap.min.css'><script type='text/javascript' src='_includes/jquery.jqplot.min.js'></script><script type='text/javascript' src='_includes/jqplot.categoryAxisRenderer.min.js'></script><script type='text/javascript' src='_includes/jqplot.pointLabels.min.js'></script><link rel='stylesheet' type='text/css' href='_css/jquery.jqplot.min.css'>"
	    var myWindow = window.open("", "popup", "width=670,height=1100,scrollbars=yes,resizable=no," +
	        "toolbar=no,directories=no,location=no,menubar=no,status=no,left=0,top=0");
	    var doc = myWindow.document;
	    doc.open();
	    $(printContents).find('button').remove();
		$(printContents).prepend($(header));
	    var button = "<input type='button' id='btnPrint' value='Print' style='float: right;' onclick='window.print();'/>";
	    $(printContents).append($(button));
	    doc.write($(printContents).html());
	    doc.close();
	});
*/
	$('.imgDisplay .reportTemplate').on('click', '.reportHead button.reportPrint', function(e) {
		$('#Imagenes, .panelCliente, .topNavegation, .imgPreview, .footerInfo').css('display', 'none');
		window.print();
		$('#Imagenes, .panelCliente, .topNavegation, .imgPreview, .footerInfo').css('display', 'block');		
		//$('.reportTemplate').css('display', 'block');		
	});

	
	// Get data from table and generate charts
/*	$('.imgDisplay .chartDisplayTemplate').on('click', '.chartBody button.makeCharts', function(e) {
		var chartHead = [];
		var chartBody = [];
		var chartData = [];
		var myHdTr = $('.chartDisplayTemplate .imgTabla thead tr:first');
		chartHead.push(myHdTr.find('th:nth-child(2)').text());
		chartHead.push(myHdTr.find('th:nth-child(3)').text());
		chartHead.push(myHdTr.find('th:nth-child(4)').text());
		chartData.push(chartHead);
		$('.chartDisplayTemplate .imgTabla tbody tr').each(function(index, element) {
            var myTr = $(this);
			chartBody = [];
			chartBody.push(myTr.find('td:nth-child(2)').text());
			chartBody.push(parseFloat(myTr.find('td:nth-child(3)').text()));
			chartBody.push(parseFloat(myTr.find('td:nth-child(4)').text()));
			chartData.push(chartBody);
		});
		//console.log(chartData);
		console.log(chartData[1][0]+" - "+chartData[1][1]+" - "+chartData[1][2]);
		drawChart(chartData);
	});
	// End Get data from table and generate charts
*/	
	
	// *New* jqPlot Get data from table and generate charts
	$('.imgDisplay .chartDisplayTemplate').on('click', '.chartBody button.makeCharts', function(e) {
		var chartMax = [];
		var chartMin = [];
		var chartTicks = [];
		var chartTitle = $('.chartDisplayTemplate .imgTabla tbody tr:first').find('td:nth-child(2)').text();
		$('.chartDisplayTemplate .imgTabla tbody tr').each(function(index, element) {
            var myTr = $(this);
			var dayOnly = myTr.find('td:nth-child(4)').text().substr(0,10);
			chartTicks.push(dayOnly);
			chartMax.push(parseFloat(myTr.find('td:nth-child(5)').text()));
			chartMin.push(parseFloat(myTr.find('td:nth-child(6)').text()));
		});
		//console.log(chartTitle+" - "+chartMax+" - "+chartMin);
		drawJqPlot(chartTitle,chartMax,chartMin,chartTicks);
	});
	// End New jqPlot Get data from table and generate charts

	// Get data from Graphics table and generate report
	$('.imgDisplay .chartDisplayTemplate').on('click', '.chartHead button.genReport', function(e) {
		var chartTitle = $('.chartDisplayTemplate .imgTabla tbody tr:first').find('td:nth-child(2)').text();
		var imgID = $('.chartDisplayTemplate .imgTabla tbody tr:first').find('td:nth-child(1)').text();
		var imgDCInfo = $('.imgPanels .imgInfoPanel.'+imgID+' .DCimgInfo').html();

		var imgURL = "";
		var imgShow = "";
		var imgInfoHed = "";
		var imgInfoBlock = "";
		var imgNameDate = "";
		var imgName = "";
		var reportTable = "";
		var reportTd =[];
		var chartMax = [];
		var chartMin = [];
		var chartTicks = [];
		$('.chartDisplayTemplate .imgTabla tbody tr').each(function(index, element) {
            var myTr = $(this);
			imgID = myTr.find('td:nth-child(1)').text();
			imgName = myTr.find('td:nth-child(3)').text();
			imgURL = $('.imgInfoPanel.'+imgID).find('img#'+imgID).attr('src');
			imgURL = imgURL.replace("_tn.jpg", ".jpg"); 
			imgShow = "<img class='"+imgID+"' src='"+imgURL+"' width='128' height='96' />";
			imgInfoHed = $('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoHed').html();
			imgInfoBlock = "<div class='chrtInfoBlock'>"+$('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoBlock').html()+"</div>";
			imgNameDate = "<strong>"+imgName+"</strong><br>"+$('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoHed p.intro').html();
			//reportTd.push("<tr><td>"+imgNameDate+"</td><td>"+imgShow+"</td><td>"+imgInfoBlock+"</td></tr>");
			reportTable += "<tr><td>"+imgNameDate+"</td><td>"+imgShow+"</td><td>"+imgInfoBlock+"</td></tr>";
			chartTicks.push(myTr.find('td:nth-child(3)').text());
			chartMax.push(parseFloat(myTr.find('td:nth-child(4)').text()));
			chartMin.push(parseFloat(myTr.find('td:nth-child(5)').text()));
		});
		//console.log(chartTitle+" - "+chartMax+" - "+chartMin);
		$('.chartDisplayTemplate .chartHead button.chartClose').click();
		genReport(chartTitle,imgDCInfo,reportTable,chartMax,chartMin,chartTicks);
	});
	// End Get data from Graphics table and generate report

	
	// open all folders on tree
	$('#Imagenes button#toggleTree').click(function() {
		if ($('#Imagenes button#toggleTree').html() === "Cerrar todas") {
			$("#treeList").jstree("close_all", -1);
			$('#Imagenes button#toggleTree').html('Abrir todas');
		} else {
			$("#treeList").jstree("open_all", 1);
			$('#Imagenes button#toggleTree').html('Cerrar todas');
		}
	});

	// Remove the clicked image panel and corresponding tab
	$('.imgDisplay .imgDisplayTemplate').on('click', '.imgHead button.imgClose', function(e) {
		// Empty every element of imgDisplayTemplate without removing
		$('.imgDisplayTemplate .imgHead h4').html('');
		$('.imgDisplayTemplate .imgBody .imgInfoBlock').html('');
		$('.imgDisplayTemplate .imgImage').html('');
		$('.imgAnalyze button.analizar').removeClass('disabled');
		$('.imgDisplay .imgAnalyze span.showTemp').html('')
		$('.imgAnalyze button.TempSpotPost').css('display', 'none');

		$('.imgDisplayTemplate').css('display', 'none');
		//$('.imgDisplay').html('');
		window.imgBig = false;
		window.imgAnalizar = false;
	});
	
	// Update image's threshold number
	$('.imgDisplay').on('click', '.imgThresholdChg button#chgThreshold', function() {
		var curVal = $('.imgPanels .imgInfoPanel.'+window.currImg+' .imgThreshold#txtThreshold').val();
		var updateVal = $('.imgThresholdChg #impThreshold').val();
		var updateLevel = 0;
		var maxTemp = $('.imgPanels .imgInfoPanel.'+window.currImg+' .imgInfoBlock tr td').filter(function() {
            return $(this).text() === "Temperatura-Max"
        }).next('td').html();
		if (maxTemp - updateVal > 20) {
			updateLevel = 2;
		} else if (maxTemp - updateVal > 10) {
			updateLevel = 1;
		}
		//console.log(window.usuarioID+", "+window.currImg+", "+updateVal+", "+updateLevel+", "+maxTemp);
		if (updateVal != curVal) {
			$.ajax({
				url:'updateThresh.php',
				type: 'POST',
				data: {
					'usuarioID' : window.usuarioID,
					'imgID' : window.currImg,
					'temp_base' : updateVal
				},
				success: function (result) {
					$('.imgPanels .imgInfoPanel.'+window.currImg+' .imgThreshold#txtThreshold').val(updateVal);
					$('#treeList').find('li#i_'+window.currImg).attr('data-range', updateLevel);
					alert(result);
						console.log(result);
				}
			});
		}
    });
	// End Update image's threshold number


	// Retrieve image temperature data file 	
	$('.imgDisplay').on('click', '.imgAnalyze button.analizar', function() {
		var myButton = $(this);
		if (window.imgMatrixID != window.currImg) {
			window.imgMatrix.length = 0;
		}
		$.ajax({
			url: "imgData.php?imgID="+window.currImg,
			cache: false}).done(function( html ) {
				// take incoming text file and store into array
				// NOT--(Old format: Assumes 160 x 120 grid of temperature data)
				// Changed to one-line files. Make grid of 160 x 120
				if (html == "Empty" || html == "NoFile") {
					window.imgAnalizar = false;
					window.imgMatrix.length = 0;
				} else {
					window.whlFile = html.split(',');
					var j=0;
					for (var i=0; i<120; i++) {
						window.eachLine = [];
						for (var x=0; x<160; x++) { 
							window.eachLine.push(window.whlFile[j]);
							j++;
						}
						window.imgMatrix[i] =  window.eachLine;
					}
					
			/*	***Old data format code
				window.whlFile = html.split('\n');
				for (var i=0; i<120; i++) {
					window.eachLine = window.whlFile[i];
					window.imgMatrix[i] = window.eachLine.split(',');
				} */
				window.imgMatrixID = window.currImg;
				}
			//$(".imgDataBlock").append(html);
		});
		if ($('.imgDataBlock .imgData.'+window.currImg)) {
			myButton.addClass('disabled');
			$('.imgDisplay .imgAnalyze span.showTemp').html('0&deg;')
			window.imgAnalizar = true;
		}
    });
	// End Retrieve image temperature data file	
	
	// Analyze image and add Temp Spots		
	$('.imgDisplayTemplate .imgImage').hover(function(e) {
		if(!window.imgAnalizar){
			return false;
		}
	}).mousemove(function(e){
		if (window.imgAnalizar) {
		   window.imgOrigin = $('.imgImage img').position();
		   var imgOffset = $('.imgImage img').offset();
			// Equate image size to data grid (i.e. image is 640x480 data is 160x120) 
		   window.curX = (Math.round((e.pageX-imgOffset.left-1)/4));
		   window.curY = (Math.round((e.pageY-imgOffset.top-1)/4));
		   window.pgPosX = Math.round(e.pageX-imgOffset.left-1);
		   window.pgPosY = Math.round(e.pageY-imgOffset.top-1);
		   if (window.curX >= 0 && window.curX < 160 && window.curY >= 0 && window.curY < 120) {
			   window.imgTemp = window.imgMatrix[window.curY][window.curX];
			   $('.imgDisplay .imgAnalyze span.showTemp').html(window.imgTemp+'&deg;');   
		   }
		}
	}).click(function(e) {
		if(window.imgAnalizar && window.curX >= 0 && window.curX < 160 && window.curY >= 0 && window.curY < 120){
			window.intSpots++;
			window.intTempSpot++;
			var tag = "<div class='tempSpot' id='temp_"+window.intTempSpot+"' style='left:"+(window.pgPosX-22)+"px; top: "+(window.pgPosY-9)+"px;' data-pgPosX='"+(window.pgPosX+22)+"' data-pgPosY='"+(window.pgPosY+9)+"' data-temp='"+window.imgTemp+"'>"+window.imgTemp+"&deg; <span><img class='xClose' src='_images/x-close.png' width='12' height='12' alt='x' /></span></div>";
			$('.imgDisplay .imgImage').append(tag);
			$('.imgAnalyze button.TempSpotPost').css('display', 'inline-block');
		} 
	}); 
	// End Analyze image and add Temp Spots		

	
	//Close a tempSpot 
	$('.imgDisplay .imgImage').on('click', '.tempSpot img.xClose', function(e) {
		e.stopPropagation();
        $(this).closest('div.tempSpot').remove();
		window.intSpots--;
		if (window.intSpots==0){
			$('.imgAnalyze button.TempSpotPost').css('display', 'none');
		} else {
			$('.imgAnalyze button.TempSpotPost').css('display', 'inline-block');
		}
    });
	// End Close a tempSpot 
		
	// Posting tempSpots to database
	$('.imgDisplay').on('click', '.imgAnalyze button.TempSpotPost', function() {
		var intSpot = 1;
		var arSpots = [];
		var strSpots = "";
		var firstSpot = true;
		var imgID = $('.imgImage img').attr('class');
		$('.imgImage .tempSpot').each(function(index) {
			// assemble comma delimited list of info for each point 
			var strSpotDet = imgID+","+(index+1)+","+$(this).attr('data-pgposx')+","+$(this).attr('data-pgposy')+","+$(this).attr('data-temp');
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
			$('.imgAnalyze button.TempSpotPost').css('display', 'none');
		});
    });
	// End Posting tempSpots to database

	// jsTree code
		$("#treeList").bind("before.jstree", function (e, data) {
            if(data.func === "delete_node") { 
				var node = data.args[0][0];
				if ($(node).find('li[rel="imgIR"]').length != 0){
					e.stopImmediatePropagation();
					alert("La carpeta tiene que estar vacia para eliminarla.");
					return false;
				}
            }
        }).jstree({  
         "themes" : { 
             "theme" : "classic" 
         },
        "types" : {
            "types" : { 
                "imgIR" : {
                    "icon" : { 
						"image" : "./_images/imgirb.png" 
                    },
					"valid_children" : "none",
					"create_node" : function() {
						return false;
					},
					"delete_node" : function() {
						alert ("Las imagenes no se pueden eliminar.");
						return false;
					},
					"hover_node" : function(obj) {
						hoverImg(obj.attr('id').substring(2), true);
					},
					"dehover_node" : function(obj) {
						hoverImg(obj.attr('id').substring(2), false);
					}
                },
				"root" : { 
                     "icon" : {  
                         "image" : "./_images/root.png" 
                     }, 
					 "max_children" : -2,
                     "valid_children" : [ "default" ], 
                     "hover_node" : false, 
                     "delete_node" : function () {return false;} /*, 
                     "select_node" : function () {return false;} */
                 }
            }
        },
        "crrm" : { 
             "move" : { 
                 "default_position" : "first", 
             } 
         },
		"core" : {
		 "animation" : 0,
		 "strings" : { loading : "Cargando ...", new_node : "Carpeta nueva" }
		 },
		 "ui" : {
		 "select_limit" : 1
		 },
		 "contextmenu" : {
			items : {
				"rename" : {
					"label" : "Cambiar nombre",
					"action" : function (obj) { this.rename(obj); 
					}
				},
				"create" : {
					"label" : "Carpeta nueva",
					"action" : function (obj) { this.create(obj); }
				},
				"remove" : {
					"label" : "Eliminar",
					"action" : function (obj) { this.remove(obj); }
				},
				"ccp" : false
			}
		},
        "json_data": {
            "data": [ 		
			window.treeFolders
			]
        },		
        "plugins" : [ "json_data", "types", "themes", "ui", "crrm", "contextmenu", "dnd" ]  
	}).bind("create.jstree", function (e, data) {
		window.folders++;
		var folderId = "f_"+(window.folders);
		data.rslt.obj.attr("id",folderId); 
		var folderName = data.rslt.name;
		var parentID = data.rslt.parent.attr('id');
		var position = data.rslt.parent.children('ul').children('li:not([rel])').length;
		console.log("Id: "+folderId+" - "+folderName+" - Parent:"+parentID+" - Parent li count:"+position);
		var orderArr = new Array();
		$('#treeList').jstree("_get_node").find('li:not([rel])').each(function(index) {	
			orderArr.push($(this).attr('id'));
		});
        $.ajax({
            url: '_includes/treeAction.php', 
            type: 'POST',
			//dataType : 'json',
            data: {
                "action" : "create",
				'usuarioID' : window.usuarioID,
				'id' : folderId,
				'nombre' : folderName,	
				'parent' : parentID,
				'pos' : position,
				'ordenArr' : orderArr
            },
            	success: function (result) {
					console.log(result);
            }
        });
		
	}).bind("rename.jstree", function (e, data) {
		var nombre = $.trim(data.rslt.obj.children('a').text());
		var id = data.rslt.obj.attr('id');
		// determine if image or folder
		var action = "renameFld";
		if (data.rslt.obj.attr('rel')=="imgIR") { 
			action = "renameImg"; 
		}
		console.log(id+" - "+nombre);
        $.ajax({
            url: '_includes/treeAction.php', 
            type: 'POST',
			//dataType : 'json',
            data: {
                "action" : action,
				'id' : id,
				'nombre' : nombre	
            },
            	success: function (result) {
					console.log(result);
            }
        });
     }).bind("remove.jstree", function (e, data) {
		var id = data.rslt.obj.attr('id');
		var action = "remove";
        $.ajax({
            url: '_includes/treeAction.php', 
            type: 'POST',
			//dataType : 'json',
            data: {
                "action" : action,
				'usuarioID' : window.usuarioID,
				'id' : id
            },
            	success: function (result) {
					console.log(result);
            }
        });
	  }).bind("move_node.jstree", function (e, data) {
			var action = "moveFld";
			if (data.rslt.o.attr('rel')=="imgIR") { 
				action = "moveImg"; 
			}
			var id = data.rslt.o.attr('id');
			var parent = data.rslt.np.attr("id");
			var orderArr = new Array();
			$('#treeList').jstree("_get_node").find('li:not([rel])').each(function(index) {	
				orderArr.push($(this).attr('id'));
			});
			var orderImgArr = new Array();
			$('#treeList').jstree("_get_node").find('li[rel]').each(function(index) {	
				orderImgArr.push($(this).attr('id'));
			});
			$.ajax({
				url: '_includes/treeAction.php', 
				type: 'POST',
				//dataType : 'json',
				data: {
					'action' : action,
					'usuarioID' : window.usuarioID,
					'id' : id,
					'parent' : parent,
					'ordenArr' : orderArr,
					'ordenImgArr' : orderImgArr
				},
				success: function (result) {
						console.log(result);
				}
			});
	     })
	// End jsTree code

});

	// Tree item Preview Called from the tree nodes
	// Changed to left side and always displays
	function hoverImg(imgID, start) {
		// retrieve info piece by piece for better control
		var imgInfoHed = $('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoHed').html();
		var imgInfoBlock = $('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoBlock').html();
		var imgDCInfo = $('.imgPanels .imgInfoPanel.'+imgID+' .DCimgInfo').html();
		var imgScroll = 0;
		var marginTop = 10;
		if (start) {
			marginTop = ($('body,html').scrollTop() - imgScroll) + marginTop;
			imgScroll = $('body,html').scrollTop();
			marginTop = $('body').scrollTop() || $('html').scrollTop();
			//$(jQuery.browser.webkit ? "body": "html").scrollTop();
			//$('.imgPreview').html($('.imgPanels .imgInfoPanel.'+imgID).html());
			$('.imgPreview').html(imgInfoHed+'<div class="imgInfoBlock">'+imgInfoBlock+'</div>'+imgDCInfo);
			//$(".imgPreview").animate({"marginTop": marginTop+"px"}, {duration:500,queue:false} );
			$('.imgPreview').animate({'top': marginTop+"px"}, {duration:500,queue:false} );
		} else {
			$('.imgPreview').empty(); 
		}
	}
	// End Tree item Preview 

	// Display image
	function displayImagen(imgID, equip) {
		//$('.imgDisplay .imgPreview').html('');
		var imgName = $('.imgInfoPanel.'+imgID+' .imgInfoHed h4').text();
		imgAnalizar = false;
		window.intSpots = 0;
		window.imgMatrix.length = 0;
						
		// use thumbnail URL minus "_tn" for big image URL
		// assumes all thumbnail images follow same pattern
		console.log(imgID);
		var imgURL = $('.imgInfoPanel.'+imgID).find('img#'+imgID).attr('src');
		imgURL = imgURL.replace("_tn.jpg", ".jpg"); 
		console.log(imgURL+" - "+imgName);
		var imgShow = "<img class='"+imgID+"' src='"+imgURL+"' width='640' height='480' />";
		var imgInfoHed = $('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoHed').html();
		var imgInfoBlock = $('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoBlock').html();
		var imgDate = "<p class='intro'>"+$('.imgPanels .imgInfoPanel.'+imgID+' .imgInfoHed p.intro').html()+"</p>";
		var imgThresh = $('.imgPanels .imgInfoPanel.'+imgID+' .imgThreshold').html();

		$('.imgDisplayTemplate .imgHead h4').html(equip+' - '+imgName);
		$('.imgDisplayTemplate .imgBody .imgInfoBlock').html(imgDate+imgInfoBlock);
		$('.imgDisplayTemplate .imgBody .imgThresholdChg').html(imgThresh);
		$('.imgDisplayTemplate .imgImage').html(imgShow);
		
		$('.imgDisplayTemplate').css('display', 'block');
		//$('body').animate({ scrollTop: 125 }, 500);
		$('body,html').animate({ scrollTop: $('.imgDisplay').offset().top }, 500);

		//$('.imgDisplay').html($('.imgDisplayTemplate').html());
		window.imgBig = true;
		window.currImg = imgID;
				
		$('.imgDataBlock .pt'+imgID).each(function(index) {
			var arPts = [];
			arPts = $(this).text().split(",");
			var tag = "<div class='tempSpot' id='temp_"+arPts[1]+"' style='left:"+(arPts[2]-45)+"px; top: "+(arPts[3]-18)+"px;' data-pgPosX='"+(arPts[2])+"' data-pgPosY='"+(arPts[3])+"' data-temp='"+arPts[4]+"'>"+arPts[4]+"&deg; <span><img class='xClose' src='_images/x-close.png' width='12' height='12' alt='x' /></span></div>";
			$('.imgDisplay .imgImage').append(tag); 
		});
	}
	// End Display image

	
	// Add lines to chart table
	function addImgInfo(imgID, equip) {
		var imgName = $('.imgInfoPanel.'+imgID+' .imgInfoHed h4').text();
		var imgDate = $('.imgInfoPanel.'+imgID+' .imgInfoHed p.intro').text();
		imgDate = imgDate.replace(", ","<br />");
		var tempMax = $('.imgInfoPanel.'+imgID+' .imgInfoBlock tbody tr:nth-child(1)').find('td:nth-child(2)').text();
		var tempMin = $('.imgInfoPanel.'+imgID+' .imgInfoBlock tbody tr:nth-child(2)').find('td:nth-child(2)').text();
		var imgChartData = "<tr>";
		imgChartData += "<td>"+imgID+"</td>";		
		imgChartData += "<td>"+equip+"</td>";
		imgChartData += "<td>"+imgName+"</td>";
		imgChartData +="<td>"+imgDate+"</td>";
		imgChartData +="<td>"+tempMax+"</td>";
		imgChartData +="<td>"+tempMin+"</td>";
		imgChartData +="<td class='delData'>"+"<span class='glyphicon glyphicon-remove'></span>"+"</td></tr>";
		//console.log(imgChartData);
		$('.chartDisplayTemplate .imgTabla tbody').append(imgChartData);
		$('.chartDisplayTemplate .chartMain').css('display', 'block');
		//$('body').animate({ scrollTop: 125 }, 500);
		$('body,html').animate({ scrollTop: $('.imgDisplay').offset().top }, 500);
	}
	// End Add lines to chart table
	
	// Google Charts function. Need to add other chart options (?)
	  function drawChart(chrtData) {
		  
        var data = google.visualization.arrayToDataTable(chrtData);

        var options = {
          title: 'Temperaturas',
		  'width':'100%',
		  'height':'100%',
		  'legend.position': 'top',
		  'lineWidth':4
        };
		$('.chartMain #chartBlock').css('min-height', '400px');
        var chart = new google.visualization.LineChart(document.getElementById('chartBlock'));
        chart.draw(data, options);
      }

		// Draw jqPlot graphic
	  function drawJqPlot(chrtTitle,chrtMax,chrtMin,chartTicks) {
		$('.chartMain #chartBlock').empty();
		$.jqplot('chartBlock', [chrtMax, chrtMin],
			{
			seriesColors: [ "#cc0000", "#3333ff" ],
			title:'Temperaturas - '+chrtTitle,
			axesDefaults: {
			  min: 0,
			  max: 100
			},
			axes: {
				xaxis: {
					renderer: $.jqplot.CategoryAxisRenderer,
					ticks: chartTicks
				},
				yaxis: {
					tickOptions: {
								  formatString: '%.1f'
								} 
				}
			},
			seriesDefaults: {
				lineWidth:5
			},
			series:[
				{label:'Temp-Max'},
				{label:'Temp-Min'}
			],
			legend: {
				show: true,
				//showSwatches: true,
				placement: 'insideGrid' //,
				//location:'ne'     // compass direction, nw, n, ne, e, se, s, sw, w.
			},
			highlighter: {
				tooltipAxes: 'y',
				show: true,
				tooltipLocation: 'n',
				formatString: '&nbsp;%.1f&deg;',
				sizeAdjust: 5
			}
		});
      }
 	// End Draw jqPlot graphic

	  
	// Generate whole report
	function genReport(reportTitle,imgDCInfo,reportTbl,chrtMax,chrtMin,chartTicks) {
		// Draw jqPlot report graphic
		$('.reportTemplate .reportHead h4').text(reportTitle);
		$('.reportTemplate .reportBody #reportImgDC').html(imgDCInfo);
		$('.reportTemplate').css('display', 'block');		

		$.jqplot('repChartBlock', [chrtMax, chrtMin],
			{
			seriesColors: [ "#cc0000", "#3333ff" ],
			title: 'Temperaturas',
			axesDefaults: {
			  min: 0,
			  max: 100
			},
			axes: {
				xaxis: {
					renderer: $.jqplot.CategoryAxisRenderer,
					ticks: chartTicks
				},
				yaxis: {
					tickOptions: {
								  formatString: '%.1f'
								} 
				}
			},
			seriesDefaults: {
				lineWidth:5,
				pointLabels: { show:true }
			},
			series:[
				{label:'Temp-Max'},
				{label:'Temp-Min'}
			],
			legend: {
				show: true,
				placement: 'insideGrid' 
			} 
		}); 
		//END chart
		$('.reportTemplate .reportTabla tbody').append(reportTbl);
		$('body,html').animate({ scrollTop: $('.imgDisplay').offset().top }, 500);
    }
		// End Draw jqPlot report graphic
