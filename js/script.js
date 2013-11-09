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

	// bing/webscrape
	/*
	$.ajax({
		url: "scraper.php",
		type: "POST",
		data: "searchTerm=" + query,
		success: function(data){
			var command = $('<input type="text" style="width:1000px" />').val(data);
			$('body').prepend(command);
			// var jsonData = $.parseJSON(data);
			// populateArticle(jsonData.articles);
			// temp/result.txt
			var but = $('body').prepend($('<button/>').on('click', function(){
				checkForFile();
			}).text('Check For File'));
		},
	});*/

	var mock = [{
		title:"Trouble in Philippines",
		summary:"The Philippine Red Cross has estimated that more than 1,000 people have been killed in the coastal city of Tacloban and at least 200 in hard-hit Samar province when one of the strongest typhoons slammed into the country.",
		//entities
		entwrap:[{
			name:query,
			type:"thing",
			relevance:100
		}]	
	},{title:"Trouble in Philippines",
		summary:"Are you in the affected area?Send us images and video, but please stay safe.",
		//entities
		entwrap:[{
			name:"affected",
			type:"adjective",
			relevance:1
		}]

	},{title:"Trouble in Philippines",
		summary:"Tacloban, Philippines (CNN) -- The destruction here is staggering: No building in this coastal city of 200,000 residents appeared Saturday to have escaped damage when Super Typhoon Haiyan roared through on Friday.",
		//entities
		entwrap:[{
			name:"coastal",
			type:"adjective",
			relevance:1
		}]
	}];

	populateArticle(mock);
}

function checkForFile(){
	$.ajax({
		url: "temp/return-result",
		async: false,
		success:function(data){
			var jsonData = $.parseJSON(data);
			populateArticle(jsonData.articles);
		}
	});
}

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
	var content = $('#articleContent');
	var highestEntity;
	var highestRelevance = 0;
	var entityNames = [];

	for(var i = 0; i < options.length; i++){
		for(var j = 0; j < options[i].entwrap.length; j++){
			entityNames.push(options[i].entwrap[j]);
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

	
	// make peopleEntity array
	var people = [];
	var things = [];
	for(var i = 0; i < options.length; i++){
		for(var j = 0; j < options[i].entwrap.length; j++){
			if(options[i].entwrap[j].type == "people"){
				people.push(options[i].entwrap[j]);
			}else{
				things.push(options[i].entwrap[j]);
			}
		}
	}

	var peopleHeader = $('<h1/>').text('People in this article');
	var peopleDiv = $('<div/>').addClass('people');
	
	for(var i = 0; i < people.length; i++){
		var inputStr = "input="+people[i].name+"&type=xml";
		

		$.ajax({
			url: "wolfram.php",
			type: "POST",
			data: inputStr,
			success: function(data){
				var peopleDiv = $('.people');

				var wolfJsonData = $.parseJSON(data);

				var name = wolfJsonData.name;
				peopleDiv.append($('<h3/>').text(name));

				var wolframData = $.parseXML(wolfJsonData.xmlCall);
				var plaintext = $(wolframData).find('plaintext').text();

				var whitespace = /(\r|\n)/
				var plaintextArr = $.map(plaintext.split(whitespace), function(e, i){
					var pipe = /\|/

					if(!whitespace.test(e)){
						var arr = e.split(pipe);
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
	
	for(var i = 0; i < things.length; i++){
		var inputStr = "input="+things[i].name+"&type=xml";
		
		$.ajax({
			url: "wolfram.php",
			type: "POST",
			data: inputStr,
			success: function(data){
				var thingsDiv = $('.things');

				var wolfJsonData = $.parseJSON(data);

				var name = wolfJsonData.name;
				thingsDiv.append($('<h3/>').text(name));

				var wolframData = $.parseXML(wolfJsonData.xmlCall);
				var plaintext = $(wolframData).find('plaintext').text();

				var whitespace = /(\r|\n)/
				var plaintextArr = $.map(plaintext.split(whitespace), function(e, i){
					var pipe = /\|/

					if(!whitespace.test(e)){
						var arr = e.split(pipe);
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


				var infoList = $('<ul/>');

				for(var i = 0; i < plaintextArr.length; i++){
					for(var prop in plaintextArr[i]){
						var property = $('<span/>').addClass('bold');
						infoList.append($('<li/>').html("<span style='font-weight:bold'>" + prop + " : </span>" +plaintextArr[i][prop]));
					}
				}

				thingsDiv.append(infoList);
			}
		});
	}

	content.append($('<h1/>').text(options[0].title).addClass('articleTitle'));
	content.append(summary);
	content.append(peopleHeader);
	content.append(peopleDiv);
	content.append(thingsHeader);
	content.append(thingsDiv);
	$('.placeholder').remove();
	$('#searchResults').fadeIn(2000);
	$('.dimmer').removeClass('active');
}

function destroyArticle(){
	$('.article').remove();
}