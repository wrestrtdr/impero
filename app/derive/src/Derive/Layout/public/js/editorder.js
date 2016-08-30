/*function recalculateNumbers() {
	var num = 1;
	$(".ordersUserNum").each(function(){
		$(this).html(num);
		num++;
	});
}

$(document).ready(function(){
	//ADDITIONS EDIT -----------------------------------------------------
	$(document).delegate(".ordersUserAddition", "click", function(){
		hash = $(this).attr("name").substring($(this).attr("name").indexOf("[") + 1, $(this).attr("name").indexOf("]"));
		
		if ($(this).prop("checked") == true) {
			$("#payment_" + hash).html(makePrice(parseFloat($("#payment_" + hash).html()) + parseFloat($(this).data("value"))));
			$("#payment").html(makePrice(parseFloat($("#payment").html()) + parseFloat($(this).data("value"))));
		} else {
			$("#payment_" + hash).html(makePrice(parseFloat($("#payment_" + hash).html()) - parseFloat($(this).data("value"))));
			$("#payment").html(makePrice(parseFloat($("#payment").html()) - parseFloat($(this).data("value"))));
		}
	});
	
	$(document).delegate(".customerEmail", "change", function(){
		var hash = $(this).parent().parent().data("hash");
		
		$.post("/users/getUserData", { email: $(this).val() }, function(data){
			data = fromJSON(data);
			
			if (data.success != true) {
				//alert(data.text);
			} else {
				$("[name='order[" + hash + "][name]']").val(data.user.name);
				$("[name='order[" + hash + "][surname]']").val(data.user.surname);
			}
		});
	});
	
	//CHANGE PACKET -----------------------------------------------------
	$(document).delegate("select.orderPacket", "change", function() {
		var hash = $(this).parent().parent().data("hash");

		if (hash.length != 40) {
			var txt = __('hash_is_missing');
			alert(txt);
			return false;
		}
		
		var sumOldAdditions = 0.0;
		$("#orderAdditions_" + hash).find("input:checked").each(function(){
			sumOldAdditions = parseFloat(sumOldAdditions) + parseFloat($(this).data("value"));
		});
		
		$.post(
			"/order/json/packetchange?tpl=editform",
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
	
	//FORM SUBMIT -----------------------------------------------------
	$(document).delegate("#btnUpdateOrder", "click", function(){
		var success = true;
		
		//validation engine checks if all data is valid
		if(!$("#orderform").validationEngine('validate'))
			return false;
		
		$(".customer").each(function(){
			hash = $(this).data("hash");
			
			if (isEmpty($("[name='order[" + hash + "][email]']").val())) {
				if (isEmpty($("[name='order[" + hash + "][name]']").val()) || isEmpty($("[name='order[" + hash + "][surname]']").val())) {
					alert(hash);
					success = false;
				}
			}
		});
		
		if (success != true) {
			var txt = __("email_or_name_and_surname_are_mandatory");
			alert();
		}
		else {
			$.post($("#orderform").attr("action"), $("#orderform").serialize(), function(data){
				data = fromJSON(data);
				
				if (data.success != true) {
					alert(data.text);
					return false;
				} else {
					redir(data.url);
				}
			});
		}
		
		return false;
	});
	
	//ADD CUSTOMER -----------------------------------------------------
	$(document).delegate("#addCustomer", "click", function(){
		$.post(
			"/order/json/addcustomer",
			{
				num: $(".customer").length,
				offer: $("#offer_id").val(),
			},
			function(data) {
				data = fromJSON(data);
				
				if (data.success != true) {
					alert(data.text);
					return false;
				}
				
				if ($(".customer").length > 1)
					$(".customer").last().after(data.html);
				else
					$("#customers").children().first().after(data.html);
				
				oldSumPayment = parseFloat($("#payment").html());
				newSumPayment = oldSumPayment + parseFloat(data.payment);
				
				$("#payment").html(newSumPayment + " €");
				
				recalculateNumbers();
			}
		);
	});
	
	// @ToDo - instead of delete, cancel user
	$(document).delegate(".btnDeleteOrdersUser", "click", function(){
		var txt = __("is_it_in_use");
		alert(txt);
		var txt2 = __('text_delete_confirmation');
		if (confirm(txt2))
			$("#customer_" + $(this).data("hash")).slideUp(function(){
				$(this).remove();
				recalculateNumbers();
			});
	});

	// @ToDo - instead of delete, cancel user
	$(document).delegate(".removeCustomer", "click", function(){
		var txt = __("confirm_delete_friend");
		if (confirm(txt)) {
			var hash = $(this).data("hash");
			$(this).hide().remove();
			$("#customer_" + hash).hide().remove();
		}
	});
});
*/