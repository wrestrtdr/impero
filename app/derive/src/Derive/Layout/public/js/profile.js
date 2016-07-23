$(document).ready(function(){
	
	$(document).delegate("#editProfile", "click", function(){
		$.post("/users/json/editprofile", function(data){
			data = fromJSON(data);
			
			if (data.success != true) {
				alert(data.text);
				return false;
			}
			
			$(".about#profileAbout").replaceWith(data.html);
			
			//updateDatepicker();
		});
	});

	$(document).delegate("#editPassword", "click", function(){
		$.post("/users/json/editpassword", function(data){
			data = fromJSON(data);
			
			if (data.success != true) {
				$(".button").before("<div class='alert alert-danger'>" + data.text + "</div>");
				
				return false;
			}
			
			$(".about#profileAbout").replaceWith(data.html);
			
			//updateDatepicker();
		});
	});
	
	//urejanje profila
	$(document).delegate("#updateProfile", "click", function(){
		$.post("/users/json/updateprofile", {
			name: $("[name='user[name]']").val(),
			surname: $("[name='user[surname]']").val(),
			email: $("[name='user[email]']").val(),
			new_password: $("[name='user[new_password]']").val(),
			phone: $("[name='user[phone]']").val(),
			address: $("[name='user[address]']").val(),
			post: $("[name='user[post]']").val(),
			dt_birth: $("[name='user[dt_birth]']").val()
		}, function(data){
			data = fromJSON(data);
			
			//ni uspešno, izpiši napako
			if (data.success != true) {
				removeAlerts();
				$(".about").append($(getAlert(data.text, 'danger')));
				return false;
			}
			
			//uspešno, zamenjaj na prikaz in izpiši sporočilo
			$(".about#profileAbout").replaceWith(data.html);
			$(".about").append($(getAlert(data.msg, 'success')));
		});
	});
	$(document).delegate("#cancelUpdate", "click", function(){
		$.post("/users/json/cancelupdate", {
			cancel: true
		}, function(data){
			data = fromJSON(data);
			
			$(".about#profileAbout").replaceWith(data.html);
		});
	});
	
	//urejanje gesla
	$(document).delegate("#updatePassword", "click", function(){
		$.post("/users/json/updatepassword", {
			user: {
				old_password: $("[name='user[old_password]']").val(),
				new_password: $("[name='user[new_password]']").val(),
				new_password2: $("[name='user[new_password2]']").val()
			}
		}, function(data){
			data = fromJSON(data);
			
			//ni uspešno, izpiši napako
			if (data.success != true) {
				removeAlerts();
				$(".about").append($(getAlert(data.text, 'danger')));
				return false;
			}
			
			//uspešno, zamenjaj na prikaz in izpiši sporočilo
			$(".about#profileAbout").replaceWith(data.html);
			$(".about").append($(getAlert(data.msg, 'success')));
		});
	});
});
