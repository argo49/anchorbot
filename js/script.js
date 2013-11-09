$(window).load(function(){
	console.log($('#content').length)

	// User Clicks Search
	$('#searchButton').on('click', submitQuery);


});

var scrollEnabled = false;

var submitQuery = function(){
	var query = $('#searchInput').val();

	console.log($('#searchInput').length + " Query: " + query);

	var dimmer = $('#dimmer');
	dimmer.addClass('active');
	shrinkHeader();

	window.setTimeout(function() {
		$('#placeholder').remove();
		$('#searchResults').fadeIn(2000, function(){
			window.setTimeout(function() {dimmer.fadeOut(400);}, 400)
			if(!scrollEnabled)scrollHeaderImageDown();
		});
	}, 1000);




	// bing/webscrape
	$.ajax({
		url: "scraper.php",
		type: "POST",
		data: "searchTerm=" + query,
		success: function(){
			console.log('Scrape Success!');
		},

	});

}

function scraperSuccess(){

}

function shrinkHeader(){
	var header = $('#header');
	header.animate({paddingTop:"20px"});
}

function expandHeader(){
	var header = $('#header');
	header.animate({paddingTop:"+=230px"});
}

function scrollHeaderImageDown(){
	scrollEnabled = true;
	var image = $('#articleImage');
	var imageHeight = image.height();
console.log("-="+(imageHeight-250)+"px");

	image.animate({top:"-="+(imageHeight-250)+"px"}, 30000, function(){
			scrollHeaderImageUp();
	});
}

function scrollHeaderImageUp(){
	scrollEnabled = true;
	var image = $('#articleImage');
	var imageHeight = image.height();
console.log("+="+(imageHeight-250)+"px");
	image.animate({top:"+="+(imageHeight-250)+"px"}, 30000, function(){
		scrollHeaderImageDown();
	});
}