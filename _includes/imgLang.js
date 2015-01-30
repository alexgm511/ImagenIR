// JavaScript Document
	$(document).ready(function() {
		var langCode = localStorage.lang? localStorage.lang : "en";
		updateLang(langCode);
		$(document).find("#chgLang").click(function(e) {
            if(langCode == "en"){
				localStorage["lang"] = "es";
			} else {
				localStorage["lang"] = "en";
			}
			var newLangCode = localStorage.lang;
			updateLang(newLangCode);
        });
	});
	function updateLang(langCode){
		$.getJSON('lang/'+langCode+'.json', function (jsdata){	
			$("[tkey]").each (function (index)
			{
				// just javascrip
				var strTr = jsdata [ this.getAttribute("tkey") ];
				this.innerHTML = strTr;
			});
		});
	}
