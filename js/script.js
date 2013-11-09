$(window).load(function(){
	console.log($('#content').length)

	// User Clicks Search
	$('#searchButton').one('click', function(){
		var query = $('#searachInput').val();
		console.log("Query: " + query);

		var dimmer = $('#dimmer');
		dimmer.addClass('active');
		shrinkHeader();

		window.setTimeout(function() {
			$('#placeholder').remove();
			$('#searchResults').fadeIn(2000, function(){
				window.setTimeout(function() {dimmer.fadeOut(400);}, 400)
			});
		}, 1000);



		// bing/webscrape
		$.ajax();



	});


});

function shrinkHeader(){
	var header = $('#header');
	header.animate({paddingTop:"20px"});
}

function expandHeader(){
	var header = $('#header');
	header.animate({paddingTop:"+=230px"});
}

