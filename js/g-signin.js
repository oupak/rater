function onSignIn(googleUser) {
	var profile = googleUser.getBasicProfile();
	var googlename = profile.getName();
	var googleemail = profile.getEmail()
	var id_token = googleUser.getAuthResponse().id_token;
	$.ajax({
		url: '/server/g-signin.php',
		type: 'POST',
		data: {
			gname: googlename,
			gemail: googleemail,
			idtoken: id_token
		},
		success: function(msg) {
			$('#signin-success-alert').toggleClass('hidden');
			setTimeout( function() {
				$('#signin-success-alert').addClass('hidden');
			}, 5000);
		}               
	});
}