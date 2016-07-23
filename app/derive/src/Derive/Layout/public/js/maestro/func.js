function redir(url) { window.location.href = url != null ? url : window.location.href; }
function open_url(url, blank) { 
	if(url != null) window.open(url, blank ? '_blank' : '' );
}

function fromJSON(data) { return JSON.parse(data); }
function toJSON(data) { return JSON.stringify(data); }

function animateReplace(selector, html, outTime, inTime, replace) {
	outTime = !outTime ? 250 : outTime;
	inTime = !inTime ? 250 : inTime;
	replace = replace !== false ? true : false;
	
	if (replace) {
		$(selector).slideUp(outTime, "linear", function() {
			//$(this).loadHtml(selector, html).fadeIn(inTime, "linear");
			$(this).replaceWith(html).fadeIn(inTime, "linear");
		});
	} else {
		$(selector).before(html);
		$(selector).prev().hide().slideDown(inTime, "linear");
	}
}

function scrollTo(element, plus){
	$('html, body').animate({scrollTop: $(element).offset().top + (typeof plus != "undefined" ? plus : 0)}, 1000);
}

function response(data) {
	ret = false;
	if (data != null) {
		if (data['e'] && data['msg']) {
			alert(data['msg']);
			ret = true;
		}
		
		if (data['html']) {
			for (var i in data['html']) {
				v = data['html'][i];
				animateReplace($(v['element']), v['content'], false, false, (v['replace'] !== false ? true : false));
			}
			ret = true;
		} else if (data['url']) {
			redir(data['url']);
			ret = true;
		}
	} else {
		return null;
	}
	
	return ret;
}

function var_dump(obj) {
    var out = '';
    for (var i in obj)
        out += i + ": " + obj[i] + "\n";

    var pre = document.createElement('pre');
    pre.innerHTML = out;
    document.body.appendChild(pre)
}

function popitup(url) {
	newwindow = window.open(url,'popupfiles','top=0,left=0,height=' + 600 + ',width=' + 800 + ',directories=false,resizable=true,menubar=false,toolbar=false');
	newwindow.focus();
	return false;
}

function isEmpty(str) {
    return (!str || 0 === str.length);
}

function isBlank(str) {
    return (!str || /^\s*$/.test(str));
}

function makePrice(num, decimal, currency) {
	if (typeof currency == "undefined")
		currency = ' €';
		
	if (typeof decimal == "undefined")
		decimal = 2;
	
	return num.toFixed(decimal) + currency;
}

function cutAndMakePrice(num, decimal, currency) {
	return makePrice(num, ((+num).toFixed(20)).replace(/^-?\d*\.?|0+$/g, '').length, currency);
}

function getCheckboxes(s) {
	if ($(s).length == 0) {
		arr = new Array();
		arr.push(0);
		return arr;
	}
		
	arr = $(s).map(function() {
		return this.value;
	}).get();
	
	return arr;
}

function getRadio(s) {
	return $(s).length == 0 ? 0 : $(s).val();
}