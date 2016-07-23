$(document).ready(function(){
	$(document).delegate("select#bills", "change", function(){
		$.post("/estimate/json/portions", { offer: $("input[name='offer_id']").val(), order: $("input[name='order_hash']").val(), portions: $(this).val() }, function(data){
			data = fromJSON(data);
			
			if (data.success != true) {
				alert(data.text);
				return;
			} else
				$(".portions").html(data.html);
		});
	});
	
	$(document).delegate("#btnSubmitEstimate", "click", function(){
		var hash = $("#estimateform").data("hash");
		var oldHtml = $("#leftcontent").html();
		
		//validation engine checks if all data is valid
		if(!$("#estimateform").validationEngine('validate'))
			return false;

		if(top !== self) { // Check we are in an iFrame
				var interval = setInterval(function(){
						if ('parentIFrame' in window) {
								clearInterval(interval);
								parentIFrame.scrollTo(0,0);
						}
				}, 32);
		}

		$("#leftcontent").slideUp(function(){
		$(this).html(__('processing_data') + '<div class="loader"></div>');
		$(this).slideDown();
		scrollTo("#leftcontent", -60);
	});

		$.post("/select-payment-method", {hash: hash, bills: $("#bills").val()}, function(data){
			if (data.redirect) {
					window.location.href = data.redirect;
					return;
			}

			if (data.success != true) {
				if (data.text)
					alert(data.text);
				else {
					logError({
						type: "response",
						title: "predracun@25",
						post: { hash: hash, bills: $("#bills").val() },
						response: data
					});
					
					var txt = __('error_try_again_or_call');
					alert(txt);
				}

				$("#leftcontent").slideUp(function(){
					$(this).html(oldHtml);
					$(this).slideDown();
					scrollTo("#leftcontent", -60);
				});

				return false;
			}
			
			$("#leftcontent").slideUp(function(){
				$("#maincontainer").removeClass(data.css.o).addClass(data.css.n);
				$(this).removeClass("span8").addClass("span12");
				$(this).html(data.html).slideDown(function(){
					scrollTo("#leftcontent", -60);
				});
				
				history.pushState({narocilnica: {html: data.html, o: "payment", n: "estimateform", oc: "span12", nc: "span8"}}, "title izbira sredstva", "/select-payment-method/" + hash);
			});
		}, "json").fail(function(data){
			logError({
				type: "request",
				title: "predracun@25",
				post: { hash: hash, bills: $("#bills").val() },
				response: data
			});
			
			var txt = __('error_try_again_or_call');
			alert(txt);
			$("#leftcontent").slideUp(function(){
				$(this).html(oldHtml);
				$(this).slideDown();
				scrollTo("#leftcontent", -60);
			});
		});
		
		return false;
	});
});
