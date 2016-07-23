// JavaScript Document
/* hoempage header video resize */
function resizeHomepageVideo() {
	video_ratio = 1920 / 1080;
	screen_ratio = $(".header.homepage").width() / $(".header.homepage").height();
	
	if(screen_ratio > video_ratio)
		$(".header.homepage video").css("height","auto").css("width", "100%");
	else
		$(".header.homepage video").css("height","100%").css("width", "auto");
}

$(document).ready(function(e) {
  resizeHomepageVideo();
});
$(window).resize(resizeHomepageVideo);

/* questionmark tooltip hover */
$(document).ready(function(e) {
	$(".tooltip-questionmark").tooltip({'position': 'auto top'});
});

/* iFrame gallery in iframe must scroll to parent position */
$(window).load(function(e) {
	setInterval(function(){
		if ('parentIFrame' in window) {
			//get all basic information about parent iframe
			parentIFrame.getPageInfo(function(parameters) {
				//set margin top for each image in gallery
				$(".blueimp-gallery-display .slides .slide img").each(function() {
					//current opened picture has transform transition 0, 0
					matrix = $(this).parent().css('transform');
					values = matrix.match(/-?[\d\.]+/g);
					
					if(values[4] == 0)
						currentImageHeight = $(this).height();
					
					slideImageOffsetTop = 
						(parameters.clientHeight / 2) -
						($(this).height() / 2) + parameters.scrollTop;
				
					$(this).css('margin-top', slideImageOffsetTop);
				});
				//set margin top for close button in gallery
				$(".blueimp-gallery-display .close").each(function() {
					$(this).css('margin-top',(parameters.clientHeight - currentImageHeight) / 2 - $(this).position().top + parameters.scrollTop);
				});
				//set margin top for close button in gallery
				$(".blueimp-gallery-display .prev, .blueimp-gallery-display .next").each(function() {
					$(this).css('margin-top', (parameters.clientHeight / 2 - $(this).height() / 2 - $(this).position().top  + parameters.scrollTop));
				});
			});
		}
	}, 16);
});

$(document).ready(function() {
	//if logout was successful, show message
	if(window.location.hash == "#logout-success") $(".alert.logout").fadeIn('fast');
	
	//open login modal if link URL is #logiModal
	$('a[href^="#loginModal"]').click(function (e) {
		e.preventDefault();
		$('#loginModal').modal('show');
		return false;
	})
	//open login modal if link URL is #logiModal
	$('a[href^="#forgotPassModal"]').click(function (e) {
		e.preventDefault();
		$('#forgotPassModal').modal('show');
		return false;
	})
	
	//open login modal if url anchor is #loginModal
	if(document.URL.substring(document.URL.lastIndexOf("#")+1) == "loginModal") {
			$("#loginModal").modal('show');
	}
	
	//login in modal box
	$("form[name='login-form']").submit(function(e) {
		e.preventDefault();
		
		$(".alert").remove();
		
		$.post("/users/login", {username:$("form[name=login-form] #email").val(), password:$("form[name=login-form] #password").val()}, function(data) {
			data = fromJSON(data);
			
			if(data.success == true) {
				redir(data.redirect);
			}
			else {
				if ($("#loginAlert").length > 0)
					$("#loginAlert").html(data.text);
				else
					$("#submit").before('<div class="alert alert-danger" id="loginAlert" data-dismiss="alert">'+data.text+'</div>');
			}
		}); 
		
		return false;
	});
	
	//forgotten password in modal
	$("form[name='forgot-pass-form']").submit(function(e) {
		e.preventDefault();
		
		$.post("/users/forgotpassword", {username:$("#emailfp").val()}, function(data) {
			data = fromJSON(data);
			
			if(data.success == true) {
					$("#submitfp").before('<div class="alert alert-success" id="forgotPasswordAlert" data-dismiss="alert">'+data.text+'</div>');
			}
			else {
					$("#submitfp").before('<div class="alert alert-danger" id="forgotPasswordAlert" data-dismiss="alert">'+data.text+'</div>');
			}
		}); 
		
		return false;
	});
    
	$('.package .more a').click(function(e) {
		e.preventDefault();
		if ($(this).parents('.package').children('.advance').css('display') == 'none')
			$(this).parents('.package').children('.advance').show(400);
		else
			$(this).parents('.package').children('.advance').hide(400);
		return false;
	});
	$('.advance .hideIt').click(function(e) {
		e.preventDefault();

		$(this).parent('.advance').hide(400);

		return false;
	});
});

//put a message in an alert box from bootstrap
function getAlert(message, status) {
	return '<div class="alert alert-' + status + '">' + message + '</div>';
}
//remove all bootstrap alerts
function removeAlerts() {
	$(".alert").remove();
}

function logError(data) {
	data.current = $("body").html();
	$.post("/log-error", data, function(){

	});
}


//IMAGE CROPPING ----------------------------------------------------------------------------
$(window).load(function(e) {
	//run function on load
	cropImages();
	//on resize run function again
	$(window).resize(function() {
		cropImages();
	});
	
	//initial function for image cropping
	function cropImages() {
		$("img.crop-responsive").each(function(index, element) {
			//get wanted ratio width:height from data-crop-ratio attribute
			if($(this).data('crop-ratio')) {
				fullRatio = $(this).data('crop-ratio').split(":");
				ratio = fullRatio[0] / fullRatio[1];
			}
			//if wanted ratio is not provided, 1:1 is default
			else
				widthRatio = heightRatio = 1;
			
			//parent container; note: padding is still overflow
			container = $(this).parent();
			//if picture is larger than the container, user can't see it
			container.css('overflow', 'hidden');
			
			//run function to resize container and images, set images margin
			cropIt($(this), container, ratio);
		});
	}
	
	function cropIt(image, container, ratio) {
		if(container.is(":visible"))
			containerHeight = container.width() / ratio;
		else
			return false;
		//resize container
		container.height(containerHeight);
	
		// Make in memory copy of image to avoid css issues
		$("<img/>")
			.attr("src", image.attr("src"))
			.load(function() {
				imageRatio = this.width / this.height;
				
				//image is too wide
				if(imageRatio > ratio) {
					image.css("max-width", "initial");
					image.height("100%");
					image.css("margin-left", (container.width() - image.width()) / 2);
				}
				//image is too tall
				else if(imageRatio < ratio) {
					image.width("100%");
					image.css("margin-top", (container.height() - image.height()) / 2);
				}
				//image ratio is just fine
				else {
				}
			});
	}
});

//scroll to function, works on iframe, user can stop it by scrolling
function globalScrollTo(target, offsetTop) {
	if(!offsetTop) offsetTop = 20;
	
	//scroll if iframe
	if('parentIFrame' in window)
		parentIFrame.scrollToOffset(0,target.offset().top - offsetTop);
	//scroll if not iframe
	else {
		page = $('html,body');
		
		//stop scrolling if user interacts
		page.on("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove", function(){
			page.stop();
		});
		
		page.animate({ scrollTop: target.offset().top - offsetTop }, 1000, function(){
			//stop scrolling if user interacts
			page.off("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove");
	 	});
	}
}

//SMOOTH SCROLLING WHEN # BUTTON/LINK -------------------------------------------------------
$(function() {
  $('a.clicknscroll, a[href="#newsletter-opt-in"]').click(function(e) {
		e.preventDefault();
	
		var target = $(this.hash);
		target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
		if (target.length) {
			globalScrollTo(target);
		}
		
		return false;
  });
});

//NEW PARALLAX FOR OFFERS AND NEWS --------------------------------------------------
$(window).load(function(e) {
	$(".offer .info-outer-box, .article .info-outer-box").each(function(index, element) {
		$(window).scroll(function(e) {
			setTop();
		});
  });
		
	setTop();
});

function setTop() {
	$(".offer .info-outer-box, .article .info-outer-box").each(function(index, element) {
		//how far from top is box
		offsetTop = $(this).offset().top - $(window).height();
		offsetTop = (offsetTop) > 0 ? offsetTop : 0;
		//speed of scrolling, smaller is faster
		speed = 6;
	
		if($(document).scrollTop() > offsetTop)
			$(this).css('top', ($(document).scrollTop() - offsetTop) / speed * -1);
	});
}
//END OF PARALLAX HOMEPAGE -------------------------------------------------------


//ACTIVE CATEGORY SCROLL HOMEPAGE -------------------------------------------------------
var selCat = '.offer-category:not(.seperator-line)';
//top spacing from window to the category
var spacingTop = 40;
var spacingBottom = 60;

$(document).load(function(e) {
	//use only if element exists
	if($(".offer-category").length) {
		category_initialize();
		category_move();
		
		//if user resizes screen, data must refresh
		var rtime;
		var timeout = false;
		var delta = 200;
		
		$(window).resize(function(e) {
			rtime = new Date();
			if (timeout === false) {
					timeout = true;
					setTimeout(resizeend, delta);
			}
		});
		function resizeend() {
			if (new Date() - rtime < delta) {
					setTimeout(resizeend, delta);
			} else {
					timeout = false;
					category_initialize();
					category_move();
			}               
		}
		
		$(window).scroll(function(e) {
			category_move();
		});
	}
});

function category_initialize() {
	//if it's mobile, just skip it
	if($(window).width() <= 767) {
		$(this).children().css('width', 'auto')
		return false;
	}
	
	//go through all categories and insert top offset and max top offset in element data
	$(selCat).each(function(index, element) {
		$(this).data('zero', $(this).offset().top);
		
		$(this).children().css('width', $(this).children().width())
		
		if($(this).nextAll(selCat).length)
			$(this).data('max', $(this).nextAll(selCat).offset().top);
	});
	//last elements max margin is equal the height of all
	$('.offer-category:not(.seperator-line):last').data('max', $('.offer.small.row:last').offset().top + $('.offer.small.row:last').outerHeight());
}
function category_move() {
	if($(window).width() > 767) {
		$(selCat).each(function(index, element) {
			//margin 0 because user is above category
			if($(window).scrollTop()+spacingTop <= $(this).data('zero')) {
				$(this).children().css('position', 'absolute').css('top', 0);
				
				if($(window).scrollTop()+spacingBottom+spacingTop > $(this).data('zero'))
					$(this).addClass('active').removeClass('inactive');
				else
					$(this).removeClass('active').addClass('inactive');
			}
			
			//user's currently at this category, which follows scrolling
			else if($(window).scrollTop()+spacingTop > $(this).data('zero') && $(window).scrollTop()+spacingTop < $(this).data('max') - spacingBottom) {
				$(this).children().css('position', 'fixed').css('top', spacingTop);
				
				$(this).addClass('active').removeClass('inactive');
			}
			
			//user is past the category so it stays at the lowest position
			else {
				diff = $(this).data('max') - $(this).data('zero') - spacingBottom;
				if($(this).children().css('position') == 'fixed')
					$(this).children().css('position', 'absolute').css('top', diff);
				
				$(this).removeClass('active').addClass('inactive');
			}
		});
		//if windows is above all categories, first one must be active
		if($(window).scrollTop()+spacingTop <= $(selCat).first().data('zero')) {
			$(selCat).first().addClass('active').removeClass('inactive');
		}
	}
}
//END OF ACTIVE CATEGORY SCROLL HOMEPAGE -------------------------------------------------------
	
// CUSTOM PLUGIN FOR TIP CLOUDS -------------------------------------------------------
(function( $ ){
	//remove all clouds
	function removeClouds() {
		$('.cloud').remove();
	}
	//this is function ot append clouds on input events
	function appendFromInput(event) {
		if(!$(this).attr('placeholder') || $(this).val())
			event.data.obj.append(event.data.cloud)
	}
	//create cloud bubble
	function createCloud(message) {
		cloud = $('<div class="cloud"></div>');
		square = $('<div class="square">' + message + '</div>').appendTo(cloud);
		triangle = $('<div class="triangle"></div>').appendTo(cloud);
		
		return cloud
	}
	
	$.fn.theCloud = function() {
		return this.each(function() {
			this.message = $(this).data('cloud-descr');
			
			//if there is no cloud message, skip to next one
			if(!this.message) return this;
			
			//create cloud element with message
			this.cloud = createCloud(this.message);
			
			//types: default, input
			this.type = $(this).data('cloud-type') ? $(this).data('cloud-type') : 'default';
				
			//work on hover
			if(this.type == 'default') {
				$(this).mouseenter(function() {
					removeClouds();
					
					$(this).append(this.cloud);
					
					//remove clouds when you click on them
					this.cloud.click(function(e) {
						removeClouds();
					});
				});
				$(this).mouseleave(function() {
					removeClouds();
				});
			}
			
			//work on input fields focus and keypress
			if(this.type == 'input') {
				if($(this).children('input').length)
					input = $(this).children('input');
				else if($(this).children('textarea').length)
					input = $(this).children('textarea');
				else
					return true;
				
				input.on('focus', {obj: $(this), cloud: this.cloud}, appendFromInput);
				input.on('keyup', {obj: $(this), cloud: this.cloud}, appendFromInput);
				
				input.on('focusout', removeClouds);
			}
		});
		
		return true;
	}
})( jQuery );
$(document).ready(function(e) {
	$('.the-cloud').theCloud();
});

/* translations */
var Translator = {

	translations: {},

	translate: function __(slug) {
		if (typeof Translator.translations[slug] !== 'undefined') {
			return Translator.translations[slug];
		}

		return slug;

		$.ajax({
			type: "POST",
			url: "/translations/json",
			data: {slug: slug},
			success: function(data) {
				data = fromJSON(data);

				Translator.translations[slug] = data.translation;
			},
			async:false
		});

		return  Translator.translations[slug];
	}

};

function __(slug) {
	return slug;
	var translation = '';

	$.ajax({
		type: "POST",
		url: "/translations/json",
		data: {slug: slug},
		success: function(data) {
			data = fromJSON(data);

			translation = data.translation;
		},
		async:false
	});

	return translation;
};