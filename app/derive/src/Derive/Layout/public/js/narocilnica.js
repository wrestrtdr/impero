/*
	function recalculateNumbers() {
		var num = 1;
		$(".ordersUserNum").each(function(){
			$(this).html(num);
			num++;
		});
	}
	window.onpopstate = function (event) {
		if (event.state.narocilnica) {
			$("#leftcontent").slideUp(function(){
				$("#maincontainer").removeClass(event.state.narocilnica.n).addClass(event.state.narocilnica.o);
				$(this).removeClass(event.state.narocilnica.cn).addClass(event.state.narocilnica.co);
				$(this).html(event.state.narocilnica.html).slideDown();
			});
		}
	}

	$(document).ready(function(){
		//javascript validation
		$('#orderform').validationEngine('attach', {validateNonVisibleFields: true});

		$(document).delegate(".btnDeleteOrdersUser", "click", function(){
			var txt = __('confirm_delete_passenger');
			if (confirm(txt))
				$("#customer_" + $(this).data("hash")).slideUp(function(){
					$(this).remove();
					recalculateNumbers();
				});
		});

		$(document).delegate(".ordersUserAddition", "click", function(){
			hash = $(this).attr("name").substring($(this).attr("name").indexOf("[") + 1, $(this).attr("name").indexOf("]"));

			if ($(this).prop("checked") == true) {
				$("#payment_" + hash).html(cutAndMakePrice(parseFloat($("#payment_" + hash).html()) + parseFloat($(this).data("value"))));
				$("#payment").html(cutAndMakePrice(parseFloat($("#payment").html()) + parseFloat($(this).data("value"))));
			} else {
				$("#payment_" + hash).html(cutAndMakePrice(parseFloat($("#payment_" + hash).html()) - parseFloat($(this).data("value"))));
				$("#payment").html(cutAndMakePrice(parseFloat($("#payment").html()) - parseFloat($(this).data("value"))));
			}
		});

		$(document).delegate("select.orderPacket", "change", function() {
			var hash = $(this).parent().parent().data("hash");

			if (hash.length != 40) {
				var txt = __("hash_is_missing");
				alert(txt);
				return false;
			}

			var sumOldAdditions = 0.0;
			$("#orderAdditions_" + hash).find("input:checked").each(function(){
				sumOldAdditions = parseFloat(sumOldAdditions) + parseFloat($(this).data("value"));
			});

			$.post(
				"/order/json/packetchange",
				{
					packet: $(this).val() > 0 ? $(this).val() : 0,
					department: getRadio($("#orderDepartments_" + hash).find("input:checked")),
					additions: getCheckboxes($("#orderAdditions_" + hash).find("input:checked")),
					hash: hash,
				},
				function(data) {
					data = fromJSON(data);

					if (data.success != true) {
						alert(data.text);
						return false;
					}

					data.payment = data.payment == null ? 0 : data.payment;
					data.payment = parseFloat(data.payment);

					$("#orderDepartments_" + hash).html(data.departments);
					$("#orderAdditions_" + hash).html(data.additions);
					$("#orderIncludes_" + hash).html(data.includes);

					var sumNewAdditions = 0.0;
					$("#orderAdditions_" + hash).find("input:checked").each(function(){
						sumNewAdditions = parseFloat(sumNewAdditions) + parseFloat($(this).data("value"));
					});

					oldCustomerPayment = parseFloat($("#payment_" + hash).html());
					oldSumPayment = parseFloat($("#payment").html());

					newCustomerPayment = data.payment + sumNewAdditions;
					newSumPayment = oldSumPayment - oldCustomerPayment + data.payment + sumNewAdditions;

					$("#payment_" + hash).html(newCustomerPayment + " €");
					$("#payment").html(newSumPayment + " €");
				}
			);
		});

		$(document).delegate("#btnSubmitOrder", "click", function(){
			var success = true;

			//check for any duplicate emails
			abort = false;

			//validation engine checks if all data is valid
			if(!$("#orderform").validationEngine('validate'))
				abort = true;

			$(".customerEmail, .payeeEmail").each(function(index, element) {
				curVal = $(this).val();
				curId = $(this).attr('id');
				duplicate = false;

				//skip if field is empty
				if(!curVal)
				 return true;

				//compare current email with all others
				$(".customerEmail, .payeeEmail").each(function(index, element) {
					//if email matches with any others but not itself (curId)
					if($(this).val() == curVal && $(this).attr('id') != curId) {
						abort = duplicate = true;
						return false;
					}
				});

				//if this email has duplicates, show an error
				if(duplicate)
					$(this).validationEngine("showPrompt", __("duplicate_emails"), "error", "topRight", true);
			});

			//form is not filled out correctly
			if(abort)
				return false;

			$("#promocode").prop("disabled", false);

			$.post("/estimate", $("#orderform").serialize(), function(data){
				history.replaceState({narocilnica: {html: $("#leftcontent").html(), o: "order", n: "estimateform", co: "span12", cn: "span8"}}, "title narocilnica", "/order");
				if (data.success != true) {
					alert(data.text);
					return false;
				}

				$("#maincontainer").prepend('<div class="loader center back"></div>');

				if('parentIFrame' in window)
					parentIFrame.scrollToOffset(0,0);
				else
					$('html,body').animate({
						scrollTop: 0
					}, 20);

				$("#leftcontent").slideUp(function(){
					$("#maincontainer").removeClass(data.css.o).addClass(data.css.n);
					//$(this).removeClass("span12").addClass("span8");
					$(this).html(data.html).slideDown(function(){
						scrollTo("#leftcontent", -60);
					}, function() {
						$('.loader').remove();
					});


						history.pushState({narocilnica: {html: data.html, o: "estimateform", n: "order", co: "span8", cn: "span12"}}, "title predracun", "/estimate");
					});
			}, "json").fail(function(data){
				logError({
					type: "request",
					title: "narocilnica@127",
					//post: { hash: hash, bills: $("#bills").val() },
					response: data
				});

				var txt = __('error_try_again_or_call');
				alert(txt);
			});

			return false;
		});

		$(document).delegate("#addCustomer", "click", function(){
			$.post(
				"/order/json/addcustomer",
				{
					num: $(".customer").length,
					offer: $("#offer_id").val()
				},
				function(data) {
					data = fromJSON(data);

					if (data.success != true) {
						alert(data.text);
						return false;
					}

					if ($(".customer").length > 1)
						$(".removeCustomer").last().after(data.html);
					else
						$("#customers").children().first().after(data.html);
					$("#appendCustomer").before(data.html);

					oldSumPayment = parseFloat($("#payment").html());
					newSumPayment = oldSumPayment + parseFloat(data.payment);

					$("#payment").html(newSumPayment + " €");

					recalculateNumbers();
				}
			);
		});

		$(document).delegate(".removeCustomer", "click", function(){
			var txt = __('confirm_delete_friend');
			if (confirm(txt)) {
				var hash = $(this).data("hash");
				//$(this).hide().remove();
				$(this).slideUp(function(){$(this).remove();});
				$("#customer_" + hash).parent().slideUp(function(){$(this).remove();});
			}
		});

		//user tries to submit a form
		$(document).delegate("#confirmPayee", "click", function(){
			hash = $("#payee").data("hash");
			empty = new Array();

			//validation engine checks if all data is valid
			if(!$("#orderform").validationEngine('validate'))
				return false;

			$("#confirmPayee").slideUp();
			$("#customers").css("opacity", 1);
			$("#customers").find("input, select").prop("disabled", false);
			$("#customers").find("input, select").attr("disabled", false);
		});

		//user sends promo code
		$(document).delegate('.promocode-submit', 'click', function(e){
			e.preventDefault();

			//field is empty
			if($('[name=promocode]').val() == "") {
				$('[name=promocode]').validationEngine("showPrompt", __("form_validation_required"), "error", "topRight", true);
				return false;
			}

			$.post(
				"/order/json/applypromocode",
				{
					promocode: $('[name=promocode]').val(),
					price: parseFloat($('.sum-all').find('span').html()),
					offer: $("#offer_id").val()
				},
				function(data) {
					data = fromJSON(data);

					if (data.success != true) {
						alert(data.text);
						return false;
					}

					$('.sum-all span').html(data.price);
					$('[name=promocode]').validationEngine("showPrompt", data.notice, "error", "topRight", true);

					if(data.valid) {
						$('[name=promocode]').addClass("positive").prop("disabled", true);

						$('.promocode-submit').prop("disabled", true).addClass('disabled');
						$('<span class="promo-code-confirmed glyphicon glyphicon-ok"></div>').hide().insertAfter('[name=promocode]').fadeIn();
					}
				}
			);

			return false;
		});


		//if there is only one departure city, make it selected
		$(".customer .inline-departure ul").each(function(index, element) {
		if($(this).find("li").length == 1)
				$(this).find("input").prop('checked', true);
	  });
	});
*/