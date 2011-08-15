function error(message) {
	return ['<div class="ui-widget ui-error"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Error:</strong> ', message, ' </p></div></div>'].join('');
}

$(document).ready(function(){
	if($.browser.msie) {
		alert("IE Not Supported");
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
				// console.log($("#regPass").val());
				// console.log($("#regConf").val());
				if($("#regPass").val() != $("#regConf").val()) {
					$("#audRegister > .ui-state-error").remove();
					$("#audRegister").prepend(error("Passwords do not match"));
					return false;
				}
				else if($("#regPass").val() === "" || $("#regConf").val() === "" || $("#regUser").val() === "") {
					$("#audRegister > .ui-state-error").remove();
					$("#audRegister").prepend(error("Please fill out all fields"));
					return false;
				}
			},
			success: function(response){
				if(response.error) {
					$("#audRegister > .ui-state-error").remove();
					$("#audRegister").prepend([error(response.error)].join(''));
				}
				else {
					$("#audPageRegister").fadeOut('fast', function(){
						$("#audPageLoading").fadeIn('fast', function(){
							$.getScript('js/Aud2.js');
						});
					});
				}
			}
		});

		// Login Form
		$("#audLogin").ajaxForm({dataType: 'json',
			beforeSubmit: function() {
				if($("#logUser").val() === "" || $("#logPass").val() === "") {
					$("#audLogin > .ui-state-error").remove();
					$("#audLogin").prepend(error("Please fill out all fields"));
					return false;
				}
			},
			success: function(response){
				if(response.error) {
					$("#audLogin > .ui-state-error").remove();
					$("#audLogin").prepend(error(response.error));
				}
				else {
					$("#audPageLogin").fadeOut('fast', function(){
						$("#audPageLoading").fadeIn('fast', function(){
							$.getScript('js/Aud2.js');
						});
					});
				}
			}
		});
		$("#audPageLogin").fadeIn('slow');
	}
});