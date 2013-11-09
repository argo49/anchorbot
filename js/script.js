$(window).load(function(){
	console.log($('#content').length)

	// User Clicks Search
	$('#searchButton').on('click', function(){
		// bing/webscrape
		$.ajax();
		shrinkHeader();

	});


});

function shrinkHeader(){
	var header = $('#header');
	var searchBox = $('#mainSearch');
	header.animate({paddingTop:"20px"});
	searchBox.animate({borderBottom:'1px solid #DDD'});
}

function expandHeader(){
	var header = $('#header');
	var searchBox = $('#mainSearch');
	header.animate({paddingTop:"250px"});
	searchBox.animate({borderBottom:'none'});

}