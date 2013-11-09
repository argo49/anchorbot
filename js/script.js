$(window).load(function(){

	// User Clicks Search
	$('#searchButton').on('click', function(){
		var query = $('input').val();
		if(query)
			submitQuery(query);

		
	});

	//user presses key
	$('input').on('keypress', function(e){
		var query = $('input').val();
		if(e.keyCode == 13 && query){
			submitQuery(query);
		}


		
	});
});

var scrollEnabled = false;

function submitQuery(query){

	console.log($('#searchInput').length + " Query: " + query);
	destroyArticle();
	createArticle();

	var dimmer = $('.dimmer');
	dimmer.addClass('active');
	shrinkHeader();
/*
	window.setTimeout(function() {
		$('.placeholder').remove();
		$('#searchResults').fadeIn(2000, function(){
			window.setTimeout(function() {dimmer.fadeOut(400);}, 400)
			if(!scrollEnabled)scrollHeaderImageDown();
		});
	}, 1000);
*/



	// bing/webscrape
	$.ajax({
		url: "scraper.php",
		type: "POST",
		data: "searchTerm=" + query,
		success: function(){
			$('.loader').text('Analyzing...');
			scraperSuccess();

		},
	});

}

var checkInterval;

function setHeaderImage(query){
	var iURL = "http://ajax.googleapis.com/ajax/services/search/images";
    $.ajax({
        url: iURL,
        type: 'GET',
        dataType: 'jsonp',
        data: {
            v:  '1.0',
            q:  query,
            format: 'json',
            jsoncallback:  '?'
        },
        success: function(data){
        	console.log("Google Data: ");
        	console.log(data);
        	var imgUrl = data.responseData.results[0].unescapedUrl;
        	$('#articleImage').attr('src',imgUrl).load(function(){
        		scrollHeaderImageDown();
        	});
        },
        error: function(xhr, textStatus, error){
            console.log(xhr.statusText, textStatus, error);
        }
        
    });
}

function scraperSuccess(){
	checkInterval = window.setInterval(checkForResults(), 500);
}

function checkForResults(){
	/*
	$.ajax({
		//mock url
		url: "lib/mock",
		type: "POST",
		success: function(data){
			console.log(data);
			//var jsonData = JSON.parse(data);
			var jsonData = [
			//article 1
			{
				title: "Heroic Dude",
				summary: "Finn the Human is the star of Adventure Time.",
				//entity 1
				entwrap: [{
					name:"finn the human",
					type:"people",
					sentiment:"righteous",
					relevance: 9001
				}]
			}]
			console.log(jsonData);
			if(data.status == "found"){
				clearInterval(checkInterval);
				populateArticle(jsonData);	
			}
		}
	});
*/

	var jsonData = [
	//article 1
	{
		title: "Heroic Dude",
		summary: "Finn the Human is the star of Adventure Time.",
		//entity 1
		entwrap: [{
			name:$('input').val(),
			type:"people",
			sentiment:"righteous",
			relevance: 9001
		}]
	}]

	populateArticle(jsonData);	
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

	if(!imageHeight){
		image.load(scrollHeaderImageDown());
	}

	image.animate({top:"-="+(imageHeight-250)+"px"}, 30000, function(){
		scrollHeaderImageUp();
	});
}

function scrollHeaderImageUp(){
	scrollEnabled = true;
	var image = $('#articleImage');
	var imageHeight = image.height();

	image.animate({top:"+="+(imageHeight-250)+"px"}, 30000, function(){
		scrollHeaderImageDown();
	});
}

function addCloseIcon(){

}

function removeCloseIcon(){

}

function closeIcon(){

}

function createArticle(){

	var article = $('<div/>')
		.addClass('article ui segment noShadow')
		.append($('<div/>')
			.addClass('ui inverted dimmer')
			.append($('<div/>')
				.addClass('ui large text loader')
				.text('Scraping the net...')));

	// everything appended to article at this point will be covered by dimmer

	var placeholder = $('<div/>').addClass('placeholder').height(500);

	var searchResults = $('<div/>').attr('id','searchResults')
		.css({display:'none'})
		// append image result to this div later!
		.append($('<div/>').attr('id','articleImageContainer')
			.append($('<img/>').attr('id','articleImage')))
		.append($('<div/>').attr('id','articleContent'));



	article.append(placeholder).append(searchResults);
	$('#content').append(article);


}

function populateArticle(options){
	console.log(options);
	var content = $('#articleContent');
	var highestEntity;
	var highestRelevance = 0;
	var entityNames = [];
	for(var i = 0; i < options.length; i++){
		for(var j = 0; j < options[i].entwrap.length; j++){
			entityNames.push(options[i].entwrap[j].name);
			if(options[i].entwrap[j].relevance > highestRelevance){
				highestRelevance = options[i].entwrap[j].relevance;
				highestEntity = options[i].entwrap[j];
			}
		}
	}

	setHeaderImage(highestEntity.name);

	// make article title

	var summary = $('<div/>').addClass('summary');

	for(var i = 0; i < options.length; i++){
		var p = $('<p/>').text(options[i].summary);
		summary.append(p);
	}


	var peopleHeader = $('<h1/>').text('People in this article');
	var peopleDiv = $('<div/>').addClass('people');
	
	// make peopleEntity array
	var people = [];
	for(var i = 0; i < options.length; i++){
		for(var j = 0; j < options[i].entwrap.length; j++){
			if(options[i].entwrap[j].type == "people"){
				people.push(options[i].entwrap[j]);
			}
		}
	}

	
	for(var i = 0; i < people.length; i++){
		var inputStr = "input="+people[i].name+"&type=xml";
		console.log("inputStr: " + inputStr);
		peopleDiv.append($('<h3/>').text(highestEntity.name));
		$.ajax({
			url: "wolfram.php",
			type: "POST",
			data: inputStr,
			success: function(data){
				var wolframData = $.parseXML(data);
				var plaintext = $(wolframData).find('plaintext').text();

				var whitespace = /(\r|\n)/
				var plaintextArr = $.map(plaintext.split(whitespace), function(e, i){
					var pipe = /\|/

					if(!whitespace.test(e)){
						var arr = e.split(pipe);
						console.log(arr);
						if(arr[0]) var prop = arr[0].trim();
						if(arr[1]) var val = arr[1].trim();
						if(prop && val) {
							prop = prop[0].toUpperCase() + prop.slice(1)
							var obj = {};
							obj[prop] = val;
						}
					} 
					if(obj)
						return obj;
				});

				console.log(plaintextArr);

				var infoList = $('<ul/>');

				for(var i = 0; i < plaintextArr.length; i++){
					for(var prop in plaintextArr[i]){
						var property = $('<span/>').addClass('bold');
						infoList.append($('<li/>').html("<span style='font-weight:bold'>" + prop + " : </span>" +plaintextArr[i][prop]));
					}
				}

				peopleDiv.append(infoList);
			}
		});
	}


	var thingsHeader = $('<h1/>').text('Other things in this article');
	var thingsDiv = $('<div/>').addClass('things');

	var things = [];
	for(var i = 0; i < options.length; i++){
		for(var j = 0; j < options[i].entwrap.length; j++){
			if(options[i].entwrap[j].type != "people"){
				things.push(options[i].entwrap[j]);
			}
		}
	}

	var thingsElements = [];
	/*
	for(var i = 0; i < things.length; i++){
		$.ajax({
			url: "http://api.wolframalpha.com/v2/query?input=" + escape(things[i].name) + "&appid=E83K2X-HP4AL66P4P",
			type: "POST",
			success: function(data){
				//toSource() this
				console.log("WolframData: " + data);
				//thingsElements.push(jquery wolfram people elements);
			}
		});
	}*/

	for(var i = 0; i < thingsElements.length; i++){
		thingsDiv.push(thingsElements[i]);
	}

	//append title
	content.append(summary);
	content.append(peopleHeader);
	content.append(peopleDiv);
	content.append(thingsHeader);
	content.append(things)
	$('.placeholder').remove();
	$('#searchResults').fadeIn(2000);
	$('.dimmer').removeClass('active');



}

function destroyArticle(){
	$('.article').remove();
}

