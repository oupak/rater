$(document).ready( function() {	
	$('#nem').click( function() {
		$('#options').toggleClass('active');
	});
	$('#logout-btn').click( function() {
		//function signOut() {
			var auth2 = gapi.auth2.getAuthInstance();
			auth2.signOut().then(function () {
			  console.log('User signed out.');
			});
		//}
	});
	$('#sidemenu-toggle').click( function() {
		$('#sidemenu').toggleClass('active');
		$('#menu-darken').toggleClass('active');
		$('body').toggleClass('no-scroll');
	});
	var key = "";
	var channel = "";
	
	string = window.location.search;
	if (string.indexOf('?q=') !== -1) {
		search = string.split('q=');
		channelSearch(key, search[1], 10);
	} else {
		console.log("Search failed");
	}
	
	$("#searchform").submit(function(event) {
		event.preventDefault();
		var search = $("#search").val();
		window.location.href = "/youtube/search.php?q="+search;
		//channelSearch(key, search[1], 10);
	});
	
	function channelSearch(key, search, maxResults) {
		$("#channels").empty();
		var url = "https://www.googleapis.com/youtube/v3/search?key="+key+"&type=channel&part=snippet&maxResults="+maxResults+"&q="+search;
		$.get(url, function(data) {
			if (data.pageInfo.totalResults == 0) {
				console.log("seark oupakd");
				window.location.href = "/rate/ouplonk.php?error=301";
			} else {
				var output;
				$.each(data.items, function(i, item) {					
					channelTitle = item.snippet.title;
					channelImage = item.snippet.thumbnails.default.url;
					channelId = item.snippet.channelId;
					output = '<a href="channel.php?c='+channelId+'"><div class="text-2xl flex items-center rounded bg-white shadow my-4 py-4"><img class="h-20 w-20 rounded-full mx-4" src="'+channelImage+'"><div>'+channelTitle+'</div></div></a>'
					$('#channels').append(output);
					
					console.log(channelImage);					
				})	
			}
		});
	}
});