function error(message) {
	$(".ui-state-error").remove();
	return ['<div class="ui-widget ui-error"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Error:</strong> ', message, ' </p></div></div>'].join('');
}

$(document).ready(function(){
	if($.browser.msie) {
		alert("IE Not Supported");
	}
	else if (AudSession) {
		$.getScript("js/Aud2.js");
	}
	else {

		$("#audRegButton").button().click(function(event){
			event.preventDefault();
			$("#audPageLogin").fadeOut('fast', function(){
				$("#audPageRegister").fadeIn('fast');
			});
			return false;
		});

		$("#audLoginButton").button().click(function(event){
			event.preventDefault();
			$("#audPageRegister").fadeOut('fast', function(){
				$("#audPageLogin").fadeIn('fast');
			});
			return false;
		});

		// Registration Form
		$("#audRegister").ajaxForm({dataType: 'json',
			beforeSubmit: function() {
				if($("#regPass").val() != $("#regConf").val()) {
					$("#audRegister").prepend(error("Passwords do not match"));
					return false;
				}
				else if($("#regPass").val() === "" || $("#regConf").val() === "" || $("#regUser").val() === "") {
					$("#audRegister").prepend(error("Please fill out all fields"));
					return false;
				}
			},
			success: function(response){
				if(response.error) {
					$("#audRegister").prepend([error(response.error)].join(''));
				}
				else {
					$("#audPageRegister").fadeOut('fast', function(){
						$("#audPageLoading").fadeIn('fast', function(){
							$.getScript("js/Aud2.js");
						});
					});
				}
			}
		});

		// Login Form
		$("#audLogin").ajaxForm({dataType: 'json',
			beforeSubmit: function() {
				if($("#logUser").val() === "" || $("#logPass").val() === "") {
					$("#audLogin").prepend(error("Please fill out all fields"));
					return false;
				}
			},
			success: function(response){
				if(response.error) {
					$("#audLogin").prepend(error(response.error));
				}
				else {
					$("#audPageLogin").fadeOut('fast');
					$("#audPageLoading").fadeIn('fast');
					$.getScript("js/Aud2.js");
				}
			}
		});
		$("#audPageLogin").fadeIn('slow');
	}
});