function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    }
    else
    {
        begin += 2;
        var end = document.cookie.indexOf(";", begin);
        if (end == -1) {
        end = dc.length;
        }
    }
    return unescape(dc.substring(begin + prefix.length, end));
}

function setCookie(name, value) {
	expiration_date = new Date();
	expiration_date.setFullYear(expiration_date.getFullYear() + 1);
	document.cookie = name + "=" + escape(value) + "; path=/; expires=" + expiration_date.toGMTString();
}

function setZekom(type) { // confirm, reject
	setCookie("zekom", type);
}

function cookiesAllowed() {
	return getCookie("zekom") == "confirm";
}

function getQueryVariable(variable)
{
 var query = window.location.search.substring(1);
 var vars = query.split("&");
 for (var i=0;i<vars.length;i++) {
         var pair = vars[i].split("=");
         if(pair[0] == variable){return pair[1];}
 }
 return(false);
}

$(document).ready(function(){
	/*if (!getCookie("zekom") || getCookie("zekom") != "confirm") {
		var pos, pos2, el, mt, h;

		if (false && getQueryVariable("ba") == "1" && getQueryVariable("ab") == "cookie") {
			pos = "top";
			pos2 = "fixed";
			el = "body";
			mt = "300px";
			h = "100%";
		} else if (true || getQueryVariable("ba") == "2" && getQueryVariable("ab") == "cookie") {
			pos = "bottom";
			pos2 = "fixed";
			el = "#maincontainer";
			mt = "8px";
			h = "auto";
		} else {
			pos = "top";
			pos2 = "absolute";
			el = ".head-main .container";
			mt = "8px";
			h = "auto";
		}

		cookieHtml = '<div id="zekom" style="position: ' + pos2 + '; ' + pos + ': 0px; z-index: 9999; width: ' + $(el).width() + 'px; height: ' + h + '; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.6);">' +
		'<span style="text-align: left; color: #fff; line-height: 20px; margin: 8px; display: block; margin: 0 auto; margin-top:' + mt + '; width: ' + $("#maincontainer").width() + 'px;">Naša spletna stran uporablja <span id="zekomMore" style="font-weight: bold; cursor: pointer;">piškotke</span> za izboljšano uporabniško izkušnjo, analizo prometa ter vtičnike socialnih omrežij. <!--Ali se strinjate z uporabo?-->' +
			'<!--<a title="Ne strinjam se z uporabo piškotov" id="zekomReject" style="float: right; margin: 1px 0px;"><img src="/img/icons/delete.png" /></a>-->' + 
			'<a title="Strinjam se z uporabo piškotov" class="button green" id="zekomConfirm" style="padding: 5px; width: 100px; float: right; margin: -1px 12px 0px 0px;">OK</a>' +
		'</span>' +
		'</div>';
		$(el).first().append(cookieHtml); // zekomConfirm, zekomReject, zekomMore
	}*/

	$(document).delegate("#zekomConfirm", "click", function() {
    $(".cookie-notice").animate({"bottom": -$(".cookie-notice").height()+"px", "opacity": 0}, 666);
		setZekom("confirm");
		redir();
	});

	/*$(document).delegate("#zekomReject", "click", function() {
		setZekom("reject");
		$("#zekom").slideUp();
	});

	$(document).delegate("#zekomMore", "click", function() {
		redir("/zakon-o-uporabi-piskotov/2/stran");
	});*/

  $(".cookie-notice").delay(3333).css("bottom", -$(".cookie-notice").outerHeight()+"px").animate({"bottom": "0", "opacity": 1}, 666);
});