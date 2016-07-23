function validateForm() {
	try {
		validator._validateForm();
	} catch (err) {
		console.log(err);
	}
}

$(document).ready(function(){
	if ($("form[name='maestroForm']").length > 0)
	$(document).delegate("input, select, textarea", "keyup change", function(){
		validateForm();

		if ($("#confirmExit").length == 0) {
			$("body").append('<input type="hidden" name="confirmExit" id="confirmExit" />');
		}
	});

	$('.extend-table').next().addClass('shrink-table');
	$('.extend-table').on('click', function(){
		$(this).next().removeClass('shrink-table');
	});
});

function FormValidatorError(errors) {
	$(".control-group").removeClass("success").removeClass("warning").removeClass("error");

	if (errors.length > 0) {
        for (var i = 0, errorLength = errors.length; i < errorLength; i++)
        	$("#" + errors[i].id).parent().parent().removeClass("success").removeClass("warning").addClass("error");

		$("#maestroFormSubmit").prop("disabled", true).addClass("disabled");
	} else {
		$("#maestroFormSubmit").prop("disabled", false).removeClass("disabled");
	}
}
	
var activeElFinder = null;
function elFinderSelectedFilesHandler(data) {
	$(activeElFinder).data("files", data).change();
}

$(document).ready(function() {
	// select all orders
	$("input[name='cbOrdersUsersAll']").click(function(){
		$("input[name='cbOrdersUsers[]']").each(function(){
			$(this).attr("checked", $("input[name='cbOrdersUsersAll']").is(":checked"));
		});
	});
	
	// add ids to batch action button and redirect to that page
	$(".btnConfirmBatch, .btnCancelBatch, .btnDeleteBatch, .btnRejectBatch").click(function(){
		var arrOrdersUsers = new Array();
		
		$("input[name='cbOrdersUsers[]']:checked").each(function(){
			var val = $(this).data('data');
			
			// push only if it is not yet in array
			if(jQuery.inArray(val, arrOrdersUsers) === -1) {
				arrOrdersUsers.push(val);
			}
			
		});
		
		// open 
		if(arrOrdersUsers.length > 0) {
			
			if (confirm("Do you really want to delete " + arrOrdersUsers.length + " orders?")) {
				open_url($(this).attr("href") + "/" + arrOrdersUsers.join(","), true);
				
				// wait a second, then reload page
				setTimeout( function() {
					redir();
				}, 1000 );
	    	}
		}
		else {
			alert('Choose at least one order!');
		}

		return false;
	});

	// send mails
	$(".btnComposeMail").click(function(){
		var arrOrdersUsers = new Array();
		$("input[name='cbOrdersUsers[]']:checked").each(function(){
			arrOrdersUsers.push($(this).val());
		});

		redir($(this).attr("href") + "?ouid=" + arrOrdersUsers.join(","));

		return false;
	});
	
	// generate all vouchers
	$(".btnGenerateAllVouchers").click(function(){
		var arrOrdersUsers = new Array();
		$("input[name='cbOrdersUsers[]']:checked").each(function(){
			arrOrdersUsers.push($(this).val());
		});

		redir($(this).attr("href") + "?ouid=" + arrOrdersUsers.join(","));

		return false;
	});

	$(".btnRecalculateBills").click(function(){
		$.post($(this).attr("href"), function(data){
			if (data.success == true) {
				if (data.hasOwnProperty("ask")) {
					if (confirm(data.ask)) {
						$.post(data.url, function(data2){
							alert(data2.text);
							if (confirm("Osvežim stran?")) {
								redir();
							}
						}, "json");
					} else {
						if (confirm("Osvežim stran?")) {
							redir();
						}
					}
				} else {
					alert(data.text);
				}
			} else {
				alert(data.text);
			}
		}, "json");
		return false;
	});
	
	$(".btnGenerateEstimate, .btnGenerateBill").click(function(){
		$.post($(this).attr("href"), function(data){
			if (data.success == true) {
				if (data.hasOwnProperty("ask")) {
					if (confirm(data.ask)) {
						redir(data.download);
					}
				}
			} else {
				alert(data.text);
			}
		}, "json");
		return false;
	});
	
	$("[name='mails_sent[mail_id]']").change(function(){
		$.get("/mails/getjson/" + $(this).val(), function(data){
			data = fromJSON(data);
			$("[name='mails_sent[from]']").val(data['sender']);
			$("[name='mails_sent[subject]']").val(data['subject']);
			tinyMCE.activeEditor.setContent(data['content']);
		});
	});
	$("[name='orders_user[packet_id]']").change(function(){
		$.post("/orders_users/packetchangediff", { original: $("#temp_packet_id").val(), updated: $(this).val() }, function(data){
			data = fromJSON(data);
			
			$("[name='orders_user[packet_id]']").parent().find(".help-block").html(data['diff'] > 0 ? "+" + data['diff'] + "€" : data['diff'] + "€").addClass(data['diff'] > 0 ? "danger" : "success").show();
		});
		
		$.post("/orders_users/packetchangedadditions", { packet: $("[name='orders_user[packet_id]']").val(), user: $("[name='orders_user[user_id]']").val() }, function(data){
			$("[id^=orders_users_additions]").multiselect("destroy");
			$("#orders_users_additions").parent().html(data);
			$("[id^=orders_users_additions]").multiselect({noneSelectedText: "Additions"});
		});
	});
	
	$(".elFinderData").click(function(){
		activeElFinder = this;
		popitup("/maestro/files");
		return false;
	});
	
	$(".btnMaestroAutoDelete").click(function(){
    	if (confirm("Do you really want to delete #" + $(this).parent().parent().children().first().html() + "?")) {
    		var This = $(this);
    		$.get($(this).attr("href"), function(data){
    			This.parent().parent().slideUp();
    		});
    	}
    	
    	return false;
    });
	
	$(".btnMaestroConfirmClick").click(function(){
    	if (confirm("Do you really want to " + $(this).attr("title") + " #" + $(this).attr("href").split("/").pop() + "?")) {
    		var This = $(this);
    		$.get($(this).attr("href"), function(data){
    			This.parent().parent().slideUp();
    			redir();
    		});
    	}
    	
    	return false;
    });
    
    $(".btnToggle").each(function(){
    	$(this).addClass($(this).data("value") == 0 || $(this).data("value") == -1 || $(this).data("value") == '0000-00-00 00:00:00' ? "btn-danger" : "btn-success");
    });
    
    $(".btnToggle").click(function() {
    	$(this).data("value", $(this).data("value") == 0 || $(this).data("value") == -1 || $(this).data("value") == '0000-00-00 00:00:00' ? 1 : 0);
    	
    	$(this).addClass($(this).data("value") == 1 ? "btn-success" : "btn-danger").removeClass($(this).data("value") == 1 ? "btn-danger" : "btn-success");
    	
    	$.post($(this).data("url") + $(this).data("value"), function(data){
    		
    	});
    });
    
	$("#maestroFormSubmit").click(function(){
		$("body").append('<input type="hidden" name="formSubmitted" id="formSubmitted" />');
	});
	
	$("#maestroFormCancel").click(function(){
		history.go(-1);
	});
	
	$('.jdt').datetimepicker({
		timeFormat: 'hh:mm:ss',
		dateFormat: "yy-mm-dd",
		separator: ' '
	});
	$(".jd").datetimepicker({
		dateFormat: "yy-mm-dd",
	});
	
	//setTimeout("$('.ufwDebug').slideUp().remove();", 5000);
	$('.ufwDebug').click(function(){
		$(this).slideUp();
	});
	
	$(".confirmSelectedOrders").click(function(){
		var orderUsers = new Array();
		$("input[name^=order_users]:checked").each(function(){
			orderUsers.push($(this).val());
		});
		
		if (orderUsers.length < 1)
			return false;
		
		window.location.href = '/maestro/orders/confirm/' + orderUsers.join(",");
	});
	
	$(".rejectSelectedOrders").click(function(){
		var orderUsers = new Array();
		$("input[name^=order_users]:checked").each(function(){
			orderUsers.push($(this).val());
		});
		
		if (orderUsers.length < 1)
			return false;
		
		window.location.href = '/maestro/orders/reject/' + orderUsers.join(",");
	});
	
	$(".cancelSelectedOrders").click(function(){
		var orderUsers = new Array();
		$("input[name^=order_users]:checked").each(function(){
			orderUsers.push($(this).val());
		});
		
		if (orderUsers.length < 1)
			return false;
		
		window.location.href = '/maestro/orders/cancel/' + orderUsers.join(",");
	});
	
	$(".toggleSelectedOrders").click(function(){
		var isCheckedFirst = $("input[name^=order_users]").first().attr("checked") == "checked";
		$("input[name^=order_users]").attr("checked", !isCheckedFirst);
	});
});
	
function imgDelete(id, type) {
	if (confirm("Želite res izbrisat sliko #" + id))
	$.post('/' + type + '_images/delete', {id: id, type: type}, function(data){
		$("#img_" + id).slideUp("slow", function(){
			$(this).detach();
		});
	});
	
	return false;
}


			$(function(){

				$('#cropbox').Jcrop({
					onSelect: updateCoords
				});

			});

			function updateCoords(c)
			{
				$('#x').val(c.x);
				$('#y').val(c.y);
				$('#w').val(c.w);
				$('#h').val(c.h);
				$('#iw').val($(".jcrop-holder").width());
				$('#ih').val($(".jcrop-holder").height());
			};

			function checkCoords()
			{
				if (parseInt())
					return true;
			};
			
			
		$(document).ready(function () {
			var jcrop_api;
			$('#settings').change(function(e) {
				if ($('#settings').val() == -1) {
					$('#cropbox').Jcrop({
						aspectRatio: 0,
						onSelect: updateCoords
					});
					$('#types').attr("value", "");
				} else if ($('#settings').val() == "4x3") {
					$('#cropbox').Jcrop({
						aspectRatio: 4/3,
						minSize: [4,3],
						onSelect: updateCoords
					});
					if ($('#types').val() != "_main" && $('#types').val() != "") {
						$('#types').attr("value", "_main");
					}
				} else if ($('#settings').val() == "150x250") {
					$('#cropbox').Jcrop({
						aspectRatio: 150/250,
						minSize: [150,250],
						onSelect: updateCoords
					});
					if ($('#types').val() != "_thumb" && $('#types').val() != "") {
						$('#types').attr("value", "_thumb");
					}
				}
			});
			
			$('#types').change(function(e) {
				if ($('#types').val() == "_main") {
					$('#settings').attr("value", "4x3");
					$('#settings').change();
				} else if ($('#types').val() == "_thumb") {
					$('#settings').attr("value", "150x250");
					$('#settings').change();
				}
			});
			
			$(".elFinderData").change(function(){
				var files = fromJSON($(this).data("files"));
				var elfinder = this;
				
				if ($(elfinder).data("multi") == -1) {
					files = files[0]; // only 1 file is allowed
					filetypes = [".jpg", ".png", ".gif", ".jpeg"];
					
					$(filetypes).each(function(key, val){
						if (files.indexOf(val) > 0) {
							files = String(files.substring(0, files.indexOf(val) + val.length)); // dafuq?
							
							$("#" + $(elfinder).data("replace")).attr("src", "/media/" + files).show();
							$("[name='" + $(elfinder).data("field") + "']").val(files);
						}
					});
					
				} else {
					$.post($(elfinder).data("url"), { files: files, id: $(elfinder).data("id") }, function(data){
						$("#" + $(elfinder).data("replace")).prev().append(data);
					});
					
					$(elfinder).data("files", null);
				}
			});
			
			$(document).on("click", ".btnDeleteGalleryPicture", function(){
				$.post($(this).data("url"));
				$(this).parent().parent().slideUp(function(){
					$(this).remove();
				});
			});
	
			// .old
		    $('#addGalleriesImages').fileupload({
		        dataType: 'text',
		        url: '/maestro/galleries_pictures/insert',
		        autoUpload: true,
		        maxNumberOfFiles: 50,
			    progressall: function (e, data) {
			    	percent = parseInt(data.loaded / data.total * 100, 10) + '%';
			        $('#addGalleriesImages').parent().find(".progress .bar").css('width', percent).html(percent);
		
			        if (percent == '100%') {
			        	setTimeout(function() {
			        		$('#addGalleriesImages').parent().find(".progress .bar").css('width', '0%').html("");
							$("#maestroFormSubmit").removeClass("disabled");
			        	}, 2500);
			        }
			    },
			    start: function (e, data) {
					$("#maestroFormSubmit").addClass("disabled");
			    },
		    });
		    
		    // .old
		    $('#addGalleriesImages').bind('fileuploaddone', function (e, data) {
		    	animateReplace("#newImageField", data["result"], false, false, false);
				equalGalleriesImageSize();
			});
		        
	        $("#yturl").live("change", function(){
	        	$('#ytvideo').attr('src', 'http://www.youtube.com/embed/' + $('#yturl').val());
	        	
	        	$("#ytpic0").attr('src', 'http://img.youtube.com/vi/' + $('#yturl').val() + '/0.jpg');
	        	$("#ytpic1").attr('src', 'http://img.youtube.com/vi/' + $('#yturl').val() + '/1.jpg');
	        	$("#ytpic2").attr('src', 'http://img.youtube.com/vi/' + $('#yturl').val() + '/2.jpg');
	        	$("#ytpic3").attr('src', 'http://img.youtube.com/vi/' + $('#yturl').val() + '/3.jpg');
	        });
		});
			
		// .old
		function equalGalleriesImageSize() {
			alert("old! /js/maestro/maestro.js:equalGalleriesImageSize()");
			return;
			var mediaEditMaxHeight = 0;
			var mediaEditNum = 0;
			$(".mediaEditImg").each(function (){
				if (mediaEditNum != 0 && mediaEditNum%6==0)
					$(this).parent().parent().addClass("ml0");
				else
					$(this).parent().parent().removeClass("ml0");
					
					
				mediaEditMaxHeight = $(this).parent().parent().height() > mediaEditMaxHeight
					? $(this).parent().parent().height()
					: mediaEditMaxHeight;
					
				mediaEditNum++;
			});
			$("#mediaEdit").children().height(mediaEditMaxHeight);
		}
		
			
			$(document).ready(function(){
				$(".divCbIncludes span input[type='checkbox'], .divCbAddition span input[type='checkbox']").click(function(){
					id = $(this).attr("id").replace("packets_addition_cb_", "").replace("packets_include_cb_", "");
					
					if ($("#packets_addition_cb_" + id).prop("checked") && $("#packets_include_cb_" + id).prop("checked"))
						if (!confirm("Selected addition/include is already selected in other section. Is it OK?"))
							$(this).prop("checked", false);
				});
				
				$(".ajax").colorbox();
				$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
				
				$('.typeahead').typeahead();
				
				$("[id^=maestroList]").each(function(){
					
					headersSet = new Array(); 
					headersSet[$(this).find("tr").find("th").length - 1] = {sorter: false};
				
			 		$(this).tablesorter({debug: false, headers: headersSet});
				});
				/*var headersSet = new Array(); 
				headersSet[($("[id^=maestroList] tr th")).length - 2] = {sorter: false};
				
			 	$("[id^=maestroList]").tablesorter({debug: false, headers: headersSet});*/
			 	
			 	// maestro tables
			 	 $("tbody.sortable").sortable({
			 	 	update: function(){
			 	 		var data = new Array();
			 	 		var i = 0;
			 	 		$("tbody.sortable tr").each(function(){
			 	 			data[i] = $(this).children().first().html();
			 	 			i++;
			 	 		});
			 	 		onSortUpdate(data);
			 	 	}
			 	 });
			 	 
			 	 $("div.sortable").sortable({
			 	 	update: function(){
			 	 		var data = new Array();
			 	 		var i = 0;
			 	 		$("div.sortable").children().each(function(){
			 	 			data[i] = $(this).first().first().children().first().val();
			 	 			i++;
			 	 		});
			 	 		onSortUpdate(data);
			 	 	},
			 	 });
			 	 
			 	 $("ul.sortable").sortable({
			 	 	update: function(){
			 	 		var data = new Array();
			 	 		var i = 0;
			 	 		$("ul.sortable").children().each(function(){
			 	 			data[i] = $(this).data("id");
			 	 			i++;
			 	 		});
			 	 		onSortUpdate(data);
			 	 	},
			 	 });
			 	 
		        $(".sortable").disableSelection();
			});
			