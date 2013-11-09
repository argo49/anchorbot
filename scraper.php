
<?php
//////////////////////////////////////
///// SCRAPER.PHP
///// Web scraper for AnchorBot
///// --------------------------
///// Takes search terms or URL, searches news outlets 
///// and scrapes the articles. The articles are turned
///// into JSON objects (each object has an array of paragraphs)
//////////////////////////////////////

function toXml($data, $rootNodeName = 'data', $xml=null)
	{
		// turn off compatibility mode as simple xml throws a wobbly if you don't.
		if (ini_get('zend.ze1_compatibility_mode') == 1)
		{
			ini_set ('zend.ze1_compatibility_mode', 0);
		}
 
		if ($xml == null)
		{
			$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
		}
 
		// loop through the data passed in.
		foreach($data as $key => $value)
		{
			// no numeric keys in our xml please!
			if (is_numeric($key))
			{
				// make string key...
				$key = "unknownNode_". (string) $key;
			}
 
			// replace anything not alpha numeric
			$key = preg_replace('/[^a-z]/i', '', $key);
 
			// if there is another array found recrusively call this function
			if (is_array($value))
			{
				$node = $xml->addChild($key);
				// recrusive call.
				toXml($value, $rootNodeName, $node);
			}
			else 
			{
				// add single node.
                                $value = htmlentities($value);
				$xml->addChild($key,$value);
			}
 
		}
		// pass back as string. or simple xml object if you want!
		return $xml->asXML();
	}
function generateAlchemyDump($url){
if(filter_var($url, FILTER_VALIDATE_URL) === TRUE){

	$file_headers = @get_headers($searchTerm);
	
	//if the URL doesn't exist, shit hits the fan and we return an error
	if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
		echo json_encode($return = array("status"=>"fail","message"=>"Sorry, that URL doesn't seem to exist (Error 404)") );
		exit();
	}
}
	//Use the AlchemyAPI to get 6 keywords of the article, so we can search other news outlets
	require_once 'lib/php/alchemyapi.php';
    $alchemyapi = new AlchemyAPI();
	$responseKeywords = $alchemyapi->entities('url', $url, array("showSourceText"=>1));
	
	//$jsonResponse=trim(json_encode($responseKeywords));
	$jsonResponse = toXml($responseKeywords);
	
	$output = urlencode("alchemy-".md5(time('ru').mt_rand()).".xml");
	
	
	$myFile = "temp/".$output;
	$fh = fopen($myFile, 'w') or die("can't open file");
	
	$enc = mb_detect_encoding($jsonResponse);
	$jsonResponse = mb_convert_encoding($jsonResponse, "ASCII", $enc);
	fwrite($fh, $jsonResponse);
	fclose($fh);
}
function scrapeAndSave($url,$output){
//Scraper code!
require_once('lib/php/simple_html_dom.php');
	
	$html = new  simple_html_dom();
	
	//parse html
	
	$html->load_file($url);  
	
	//find all the p tags
	$paragraphs = $html->find('p');  
	
	$paragraphResult = array();
	
	foreach($paragraphs as $paragraph){
	
		$paragraphResult[] = strip_tags($paragraph->innertext);
	
	}
	$jsonReturn["url"] = $url;
	$jsonReturn["paragraphs"] = $paragraphResult;
	//$jsonReturn = json_encode($jsonReturn);
	$jsonReturn = toXml($jsonReturn);
	
	//save the file
	$myFile = "temp/".$output;
	$fh = fopen($myFile, 'w') or die("can't open file");
	$enc = mb_detect_encoding($jsonReturn);
	$jsonReturn = mb_convert_encoding($jsonReturn, "ASCII", $enc);
	fwrite($fh, $jsonReturn);
	fclose($fh);
}

function bingSearchAndScrape($term, $site){

//////////////////////
// API ACCOUNT STUFF
//////////////////////

//Key (joseph@szymborski.ca)
$accountKey = 'J/rpu1glAq2f1xaMC5ZWs7/BOH94Fexp0caKokbeWaY';
//for basic auth
$auth = base64_encode("$accountKey:$accountKey");

//Array that'll be for context
$data = array(
  'http'            => array(
  'request_fulluri' => true,
  'ignore_errors'   => true,
  'header'          => "Authorization: Basic $auth")
);

//A bunch of header stuff
$context   = stream_context_create($data);
$serviceOp = 'Web';
$market    = 'en-us';
$ServiceRootURL = 'https://api.datamarket.azure.com/Bing/Search/'; 
//URL template 
$WebSearchURL   = $ServiceRootURL . 'Web?$format=json&Query=';



//Query (sexy stuff)
$query     = "site:".$site." ".$term;


//Request URL
//we urldecode here before the encode to make sure we don't encode twice
$request = $WebSearchURL . "%27". urlencode($query). "%27";

// Get the response from Bing.
$response = file_get_contents($request, 0, $context);
//below response variable is an example response for debugging.
//$response = '{"d":{"results":[{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=0&$top=1","type":"WebResult"},"ID":"965f9531-4fff-4920-b1c9-288819887886","Title":"ObamaCare Facts: Facts on the Affordable Care Act","Description":"We present the facts on Obama Care (ObamaCare), the health care plan for America. Our goal is to help you understand the Affordable Care Act.","DisplayUrl":"obamacarefacts.com/obamacare-facts.php","Url":"http://obamacarefacts.com/obamacare-facts.php"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=1&$top=1","type":"WebResult"},"ID":"ee9472a7-478f-465d-b60c-f6d7c9e49a45","Title":"ObamaCare - Truths, Myths and Lies of The Patient Protection and ...","Description":"ObamaCare Truths, Myths and Lies of The Patient Protection and Affordable Care Act (PPACA)","DisplayUrl":"obamacare.net","Url":"http://obamacare.net/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=2&$top=1","type":"WebResult"},"ID":"c633ae82-6498-4f37-a0ba-4ac041a44ca3","Title":"Patient Protection and Affordable Care Act - Wikipedia, the free ...","Description":"The Patient Protection and Affordable Care Act (PPACA), commonly called the Affordable Care Act (ACA) or \"Obamacare\", is a United States federal statute signed into ...","DisplayUrl":"en.wikipedia.org/wiki/Patient_Protection_and_Affordable_Care_Act","Url":"http://en.wikipedia.org/wiki/Patient_Protection_and_Affordable_Care_Act"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=3&$top=1","type":"WebResult"},"ID":"01355490-a494-4494-b3e0-5dc9adaefbb4","Title":"ObamaCare - Conservapedia","Description":"ObamaCare is opposed by many state governors, including Florida Governor Rick Scott. He declared after the Supreme Court ruling, \"I will not implement this law.","DisplayUrl":"conservapedia.com/ObamaCare","Url":"http://conservapedia.com/ObamaCare"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=4&$top=1","type":"WebResult"},"ID":"ed1f81f8-1c49-4e4e-a65c-98c2d7bc6603","Title":"ObamaCare | | ObamaCare Facts | Health Insurance Information ...","Description":"Your news and information source for the facts about ObamaCare, health insurance, and the Affordable Care Act.","DisplayUrl":"www.obamacare.com","Url":"http://www.obamacare.com/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=5&$top=1","type":"WebResult"},"ID":"610e7cf5-807e-4f79-8b8d-1ca973cf6dd5","Title":"Impact of Obamacare: Facts, Statistics, Charts and Videos About ...","Description":"The real impact Obamacare will have on the uninsured is not what many Americans might have expected. If implemented as enacted, Obamacare will impose significant new ...","DisplayUrl":"www.heritage.org/research/projects/impact-of-obamacare","Url":"http://www.heritage.org/research/projects/impact-of-obamacare"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=6&$top=1","type":"WebResult"},"ID":"c72ec1fd-fdb8-4907-a1f7-d80d423c985b","Title":"What is ObamaCare / What is Health Care Reform?","Description":"What is ObamaCare?: ObamaCare is the unofficial name for The Patient Protection and Affordable Care Act which was signed into law on March 23, 2010.","DisplayUrl":"obamacarefacts.com/whatis-obamacare.php","Url":"http://obamacarefacts.com/whatis-obamacare.php"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=7&$top=1","type":"WebResult"},"ID":"d857be2d-b4b8-4d2e-8a7f-f13777eac592","Title":"All apologies: Biden, Sebelius sorry for Obamacare site debacle ...","Description":"Visitors trying to log on to the Obamacare website early Thursday saw the same phrase that has roiled users for weeks: \"The system is down at the moment.\"","DisplayUrl":"www.cnn.com/2013/10/30/politics/obamacare-sebelius/index.html","Url":"http://www.cnn.com/2013/10/30/politics/obamacare-sebelius/index.html"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=8&$top=1","type":"WebResult"},"ID":"e1831e5f-b8b6-4b8a-9b4c-5b0a216f751c","Title":"Obamacare Facts - About.com US Economy","Description":"What is Obamacare? It requires you to have health insurance by March 31 2014. Find out the facts you need to know now about how it affects you, its costs, taxes and ...","DisplayUrl":"useconomy.about.com/od/healthcarereform/f/What-Is-Obama-Care.htm","Url":"http://useconomy.about.com/od/healthcarereform/f/What-Is-Obama-Care.htm"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=9&$top=1","type":"WebResult"},"ID":"442fc512-afa4-4234-b0f5-19c1f9d342c9","Title":"Obamacare - The Huffington Post","Description":"Big News on Obamacare. Includes blogs, news, and community conversations about Obamacare.","DisplayUrl":"www.huffingtonpost.com/news/obamacare","Url":"http://www.huffingtonpost.com/news/obamacare"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=10&$top=1","type":"WebResult"},"ID":"d456fdbc-5f0d-4014-a939-4fa3526501bb","Title":"Obamacare - The Heritage Foundation","Description":"Research, data, and analysis Obamacare, or President Obama\u0027s health care reform proposals.","DisplayUrl":"www.heritage.org/issues/health-care/obamacare","Url":"http://www.heritage.org/issues/health-care/obamacare"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=11&$top=1","type":"WebResult"},"ID":"66e1c7cf-7cd6-40a5-b758-1f1084903906","Title":"Obamacare website\u0027s confusing prices - Nov. 7, 2013","Description":"The Obamacare website offers premium information only for those age 27 and 50 in its plan browser feature. That may lowball the actual premium prices for many.","DisplayUrl":"money.cnn.com/2013/11/07/news/economy/obamacare-prices/index.html","Url":"http://money.cnn.com/2013/11/07/news/economy/obamacare-prices/index.html"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=12&$top=1","type":"WebResult"},"ID":"cc67892a-0ac6-4afc-9d17-1360aaab0658","Title":"How Obamacare will change employer-provided insurance - CBS News","Description":"Millions of Americans are being informed they\u0027re being dropped from their insurance plans because the plans don\u0027t meet minimum Obamacare standards, but ...","DisplayUrl":"www.cbsnews.com/8301-250_162-57611192/how-obamacare-will-change...","Url":"http://www.cbsnews.com/8301-250_162-57611192/how-obamacare-will-change-employer-provided-insurance/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=13&$top=1","type":"WebResult"},"ID":"9bff5f27-2bee-4ca7-aaff-878f24149c5c","Title":"ObamaCare News and Video - FOX News Topics - FOXNews.com","Description":"Watch breaking news videos and read news updates about ObamaCare on FOXNews.com.","DisplayUrl":"www.foxnews.com/topics/obamacare.htm","Url":"http://www.foxnews.com/topics/obamacare.htm"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=14&$top=1","type":"WebResult"},"ID":"6b7c64dc-5ad9-4b5e-bf22-f2cfe7429919","Title":"ObamaCare Watch","Description":"In recent months, President Obama and his subordinates have waived or delayed a number of Obamacareâ€™s notable features, such as the lawâ€™s employer mandate, and ...","DisplayUrl":"obamacarewatch.org","Url":"http://obamacarewatch.org/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=15&$top=1","type":"WebResult"},"ID":"9d04fd80-b4ca-4bc4-9ff4-5e27f626becf","Title":"ObamaCare | NewsBusters","Description":"On Tuesday, CNN\u0027s Carol Costello lectured Republicans to stop grilling HHS Secretary Sebelius over ObamaCare and \"sit down with Democrats to come up with some solutions.\"","DisplayUrl":"newsbusters.org/other-topics/obama-watch/obamacare","Url":"http://newsbusters.org/other-topics/obama-watch/obamacare"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=16&$top=1","type":"WebResult"},"ID":"a4501a58-52e3-441a-8f7c-a39a812b7ee5","Title":"ObamaCare | American Center for Law and Justice ACLJ","Description":"Jay Sekulow and the ACLJ have been battling against ObamaCare - the most pro-abortion law and...","DisplayUrl":"aclj.org/obamacare","Url":"http://aclj.org/obamacare"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=17&$top=1","type":"WebResult"},"ID":"c5f6396b-5d27-45c6-a5dd-e833668d3e27","Title":"Obamacare\u0027s critics justified: Our view","Description":"But the pre-Obamacare status quo is not a viable option. Its runaway costs and sleazy insurance practices were placing increasing millions of people in ...","DisplayUrl":"www.usatoday.com/story/opinion/2013/11/03/obamacare-coverage...","Url":"http://www.usatoday.com/story/opinion/2013/11/03/obamacare-coverage-cancellations-president-obama-promise-editorials-debates/3426681/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=18&$top=1","type":"WebResult"},"ID":"1082e192-fd8d-4aed-a2ef-e0d27422a89f","Title":"Fact-Checking The President\u0027s Kind-Of Sort-Of \u0027Apology\u0027 For ...","Description":"Pharma & Healthcare | 11/08/2013 @ 6:04AM | 68,816 views Fact-Checking The President\u0027s Kind-Of Sort-Of \u0027Apology\u0027 For Obamacare-Driven Insurance ...","DisplayUrl":"www.forbes.com/sites/theapothecary/2013/11/08/fact-checking-the...","Url":"http://www.forbes.com/sites/theapothecary/2013/11/08/fact-checking-the-presidents-kind-of-sort-of-apology-for-obamacare-driven-insurance-cancellations/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=19&$top=1","type":"WebResult"},"ID":"41bf4a4d-45e0-4065-9824-578e7264b6b7","Title":"Obamacare news, articles and information: - Natural health news","Description":"Obamacare isn\u0027t about health, it\u0027s about CONTROL 8/8/2013 - Those who supported the effort by President Obama and fellow Democrats to \"reform\" the nation\u0027s healthcare ...","DisplayUrl":"www.naturalnews.com/Obamacare.html","Url":"http://www.naturalnews.com/Obamacare.html"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=20&$top=1","type":"WebResult"},"ID":"f0e4f863-0a1a-473a-8dbe-1f883846acd6","Title":"Sebelius: Obamacare Oct. Sign-Ups Will Be â€˜Quite Lowâ€™ - ABC News","Description":"Health and Human Services Secretary Kathleen Sebelius predicted Wednesday that the October enrollment figures released next week will likely be â€œquite ...","DisplayUrl":"abcnews.go.com/blogs/politics/2013/11/sebelius-obamacare-oct-sign...","Url":"http://abcnews.go.com/blogs/politics/2013/11/sebelius-obamacare-oct-sign-ups-will-be-quite-low/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=21&$top=1","type":"WebResult"},"ID":"3e18eef8-3554-4b69-8fe4-5cd0764e5359","Title":"DC ObamaCare numbers show 5 people signed up for exchange | Fox News","Description":"While President Obama continues his cross country pitch on the merits of his landmark health care law, dismal new data shows only five people in the D.C ...","DisplayUrl":"www.foxnews.com/politics/2013/11/08/dc-obamacare-numbers-show-5...","Url":"http://www.foxnews.com/politics/2013/11/08/dc-obamacare-numbers-show-5-people-signed-up-for-exchange/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=22&$top=1","type":"WebResult"},"ID":"cf03a1de-be27-41fd-84fd-324f5175b1bd","Title":"In the messaging war on Obamacare, both sides get personal - Yahoo ...","Description":"WASHINGTON (Reuters) - Wayne Dofflemyer is no fan of Obamacare. Because of President Barack Obama\u0027s healthcare overhaul, Dofflemyer\u0027s insurance company ...","DisplayUrl":"news.yahoo.com/messaging-war-obamacare-both-sides-personal...","Url":"http://news.yahoo.com/messaging-war-obamacare-both-sides-personal-061135246--sector.html"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=23&$top=1","type":"WebResult"},"ID":"2e129249-d579-4b06-a107-691e22966608","Title":"Obamacare | TheBlaze.com - Breaking news and opinion on TheBlaze","Description":"TheBlaze is a news, information and opinion site brought to you by a dedicated team of writers, journalists & video producers. Our goal is to post, report and analyze ...","DisplayUrl":"www.theblaze.com/news/obamacare","Url":"http://www.theblaze.com/news/obamacare/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=24&$top=1","type":"WebResult"},"ID":"7d727b7f-c52e-4eed-9456-5084834dd761","Title":"Text of the Obamacare bill and ruling | - Brain Shavings","Description":"Obamacare. The Affordable Care Act. The health care bill. Whatever name you call it by, it affects you in more ways than you know. No, it has not been repealed.","DisplayUrl":"brainshavings.com/obamacare","Url":"http://brainshavings.com/obamacare/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=25&$top=1","type":"WebResult"},"ID":"3c2e3173-7088-41f8-8c36-dd146920758c","Title":"After apology on Obamacare, Obama faces bipartisan squeeze ...","Description":"After apology on Obamacare, Obama faces bipartisan squeeze. President Obama said he is \u0027sorry\u0027 about people who are losing insurance plans they like, but ...","DisplayUrl":"www.csmonitor.com/USA/DC...on-Obamacare-Obama-faces-bipartisan-squeeze","Url":"http://www.csmonitor.com/USA/DC-Decoder/2013/1108/After-apology-on-Obamacare-Obama-faces-bipartisan-squeeze"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=26&$top=1","type":"WebResult"},"ID":"2442b2bf-05d2-40ce-a02d-f505dd85e694","Title":"Obamacare - TIME - News, pictures, quotes, archive","Description":"Obamacare news and background. Articles, pictures, videos, specials and TIME covers about Obamacare.","DisplayUrl":"topics.time.com/obamacare","Url":"http://topics.time.com/obamacare/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=27&$top=1","type":"WebResult"},"ID":"34f3067b-d36a-4685-bab2-10310653aa7e","Title":"Obamacare Troubles Inspire Leaks From a Once-Tight-Lipped White ...","Description":"For nearly five years, the people who toil inside the Obama administration have been pretty good about refraining from anonymously sniping at one another ...","DisplayUrl":"www.businessweek.com/articles/2013-11-05/obamacare-troubles...","Url":"http://www.businessweek.com/articles/2013-11-05/obamacare-troubles-inspire-leaks-from-a-once-tight-lipped-white-house"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=28&$top=1","type":"WebResult"},"ID":"6f60f1a9-9a2d-4625-9608-8f70dbce0b0f","Title":"Sebelius on offense, tells Senate panel she won\u0027t delay Obamacare ...","Description":"WASHINGTON â€” Health and Human Services Secretary Kathleen Sebelius said Wednesday that she had considered shutting down the problematic Obamacare website, but has ...","DisplayUrl":"www.latimes.com/...sebelius-obamacare-delay-20131106,0,1021952.story","Url":"http://www.latimes.com/nation/politics/politicsnow/la-pn-sebelius-obamacare-delay-20131106,0,1021952.story"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=29&$top=1","type":"WebResult"},"ID":"7635770b-8749-4ae2-b1fe-996b9907ea75","Title":"Just 4 People Have Enrolled In Obamacare In Delaware","Description":"WILMINGTON, Del. -- WILMINGTON, Del. (AP) â€” More than a month after the launch of Delaware\u0027s health insurance exchange, officials report only four ...","DisplayUrl":"www.huffingtonpost.com/2013/11/06/obamacare-delaware-enrollments_n...","Url":"http://www.huffingtonpost.com/2013/11/06/obamacare-delaware-enrollments_n_4228386.html"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=30&$top=1","type":"WebResult"},"ID":"3f2dc402-a333-46d7-a328-dc116132d5b1","Title":"The Morning Plum: White House tries to stanch Obamacare bleeding","Description":"Yesterday, in an interview with NBC Newsâ€™ Chuck Todd, President Obama apologized to those who have seen insurance cancelled due to Obamacare, conceding many ...","DisplayUrl":"www.washingtonpost.com/.../11/08/the-morning-plum...obamacare-bleeding","Url":"http://www.washingtonpost.com/blogs/plum-line/wp/2013/11/08/the-morning-plum-white-house-tries-to-staunch-obamacare-bleeding/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=31&$top=1","type":"WebResult"},"ID":"656db297-cc89-4cfe-8932-794432799d58","Title":"4 Ways to Fix Obamacare | Daily Ticker - Yahoo Finance","Description":"From the blog Daily Ticker: As clumsy as the launch of the Affordable Care Act has been, the law is here to stay. But its numerous provisions arenâ€™t ...","DisplayUrl":"finance.yahoo.com/blogs/daily-ticker/4-ways-fix-obamacare...","Url":"http://finance.yahoo.com/blogs/daily-ticker/4-ways-fix-obamacare-174242324.html"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=32&$top=1","type":"WebResult"},"ID":"2769de07-1866-4a23-b7a2-a9673a67e46a","Title":"Obamacare â€” Barack Obama - Organizing for Action","Description":"Thanks to the Supreme Court ruling, Obamacare can keep saving lives and making health care more affordable for millions of Americans. Say youâ€™re with President ...","DisplayUrl":"www.barackobama.com/i-like-obamacare","Url":"http://www.barackobama.com/i-like-obamacare/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=33&$top=1","type":"WebResult"},"ID":"fcc46f35-03ab-4b1e-a8ea-b9c2d83e82db","Title":"This Is Why We Need Obamacare - NYTimes.com","Description":"No, far more serious is the kind of catastrophe facing people like Richard Streeter, 47, a truck driver and recreational vehicle repairman in Eugene, Ore ...","DisplayUrl":"www.nytimes.com/.../sunday/kristof-this-is-why-we-need-obamacare.html","Url":"http://www.nytimes.com/2013/11/03/opinion/sunday/kristof-this-is-why-we-need-obamacare.html"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=34&$top=1","type":"WebResult"},"ID":"352a09b2-a466-49bb-84a5-971237822238","Title":"Obamacare financial definition of Obamacare. Obamacare finance ...","Description":"Obamacare An informal term for the Patient Protection and Affordable Care Act and the Health Care and Education Reconciliation Act of 2010. The two bills changed how ...","DisplayUrl":"financial-dictionary.thefreedictionary.com/Obamacare","Url":"http://financial-dictionary.thefreedictionary.com/Obamacare"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=35&$top=1","type":"WebResult"},"ID":"a60a900b-2117-4bc2-894f-991c7b7d4a2c","Title":"Thanks, Obamacare","Description":"Opinions aggregated here from external sources are theirs alone, and do not reflect the opinion of CCHI/ProgressNow or any employee, officer or director thereof.","DisplayUrl":"www.thanksobamacare.org","Url":"http://www.thanksobamacare.org/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=36&$top=1","type":"WebResult"},"ID":"da7b7067-270b-495b-bad1-68981ec895a8","Title":"Tea partier shifts tactics on Obamacare - Jake Sherman - POLITICO.com","Description":"PLAINVILLE, Kan. â€” Reality has sunk in for one of Congressâ€™s staunchest Obamacare opponents. Just weeks ago, Rep. Tim Huelskamp was one of the most ...","DisplayUrl":"www.politico.com/story/2013/...shifts-tactics-on-obamacare-99567.html","Url":"http://www.politico.com/story/2013/11/tea-partier-shifts-tactics-on-obamacare-99567.html"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=37&$top=1","type":"WebResult"},"ID":"01a79dae-f985-4d19-962a-30ee6e0a6e4d","Title":"Obamacare (obamacare) on Twitter","Description":"The latest from Obamacare (@obamacare). Connecting you with news and information about the Affordable Care Act, one tweet at a time. Run by Organizing for Action ...","DisplayUrl":"twitter.com/obamacare","Url":"http://twitter.com/obamacare"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=38&$top=1","type":"WebResult"},"ID":"b819d355-61e9-4b2f-a152-ffe55b02529d","Title":"Amazon.com: obamacare","Description":"The Patient Protection and Affordable Care Act (Obamacare) w/full table of contents by Barack Obama and The 111th Congress (Kindle Edition - Dec. 5, 2012) - Kindle eBook","DisplayUrl":"www.amazon.com/s?ie=UTF8&page=1&rh=i%3Aaps%2Ck%3Aobamacare","Url":"http://www.amazon.com/s?ie=UTF8&page=1&rh=i%3Aaps%2Ck%3Aobamacare"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=39&$top=1","type":"WebResult"},"ID":"27d9baec-02a9-476d-8647-10f272ada89f","Title":"Articles about Obamacare - Los Angeles Times","Description":"Obamacare News. Find breaking news, commentary, and archival information about Obamacare From The Los Angeles Times","DisplayUrl":"articles.latimes.com/keyword/obamacare","Url":"http://articles.latimes.com/keyword/obamacare"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=40&$top=1","type":"WebResult"},"ID":"0d0490f8-f599-4d8a-aade-3c345ce87e5e","Title":"Obamacare | Facebook","Description":"Obamacare. 688,671 likes Â· 67,647 talking about this. This page is run by Organizing for Action. www.barackobama.com","DisplayUrl":"https://www.facebook.com/ilikeobamacare","Url":"https://www.facebook.com/ilikeobamacare"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=41&$top=1","type":"WebResult"},"ID":"b4e87305-7f32-45ea-9275-4e35640b1c5a","Title":"Only six signed up for ObamaCare on first day | New York Post","Description":"Only six people signed up for ObamaCare on the federal health insurance Web site during its problem-plagued first day of operation, according to a shocking ...","DisplayUrl":"nypost.com/2013/11/01/only-six-signed-up-for-obamacare-on-first-day","Url":"http://nypost.com/2013/11/01/only-six-signed-up-for-obamacare-on-first-day/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=42&$top=1","type":"WebResult"},"ID":"41274364-7d2c-4a2b-b8f8-41accd08304a","Title":"Obamacare | The Weekly Standard","Description":"Obama administration officials produced a memo documenting security concerns with healthcare.gov, the health insurance exchange website, the Associated Press reports:","DisplayUrl":"www.weeklystandard.com/keyword/Obamacare","Url":"http://www.weeklystandard.com/keyword/Obamacare"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=43&$top=1","type":"WebResult"},"ID":"d6b6a38f-77f3-4f68-8cbf-29a62ded3bda","Title":"Obamacare site is a \u0027long way from where it needs to be\u0027: Zients","Description":"The still \"very slow\" federal Obamacare insurance website \"remains a long way from where it needs to be,\" the management guru responsible for getting it ...","DisplayUrl":"www.cnbc.com/id/101183580","Url":"http://www.cnbc.com/id/101183580"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=44&$top=1","type":"WebResult"},"ID":"440c38d3-96de-4f44-846f-7445d2e67f52","Title":"The Obamacare \u0027scandal\u0027 you haven\u0027t heard about â€“ CNN Belief ...","Description":"Few Bible Belt pastors mention what\u0027s in their backyard, millions of poor people trapped in the Obamacare â€œcoverage gap.â€","DisplayUrl":"religion.blogs.cnn.com/2013/11/08/the-obamacare-question-pastors-shun","Url":"http://religion.blogs.cnn.com/2013/11/08/the-obamacare-question-pastors-shun/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=45&$top=1","type":"WebResult"},"ID":"0d785e5f-3e9d-42d0-8802-f33879bc1edc","Title":"Rep. Fred Upton: Keep-Your-Plan Bill Fixes Big Obamacare Problem","Description":"Americans want an antidote to President Barack Obama\u0027s big lie that they could keep their healthcare coverage if they want to, and that\u0027s what Rep. Fred ...","DisplayUrl":"www.newsmax.com/Newsfront/fred-upton-obamacare-keep-plan/2013/11/...","Url":"http://www.newsmax.com/Newsfront/fred-upton-obamacare-keep-plan/2013/11/08/id/535718"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=46&$top=1","type":"WebResult"},"ID":"92f1c120-0dfd-4b98-87a7-008f0feaae01","Title":"Obamacare | Fox News Insider","Description":"Fox News Insider articles tagged with \"Obamacare\" - 1 ... 1 hour 13 min ago \u0027Marriage Isn\u0027t for Me\u0027: Recently-Married Blogger Stirs Up Controversy","DisplayUrl":"foxnewsinsider.com/tag/obamacare","Url":"http://foxnewsinsider.com/tag/obamacare/"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=47&$top=1","type":"WebResult"},"ID":"e4e7c41b-0905-41d8-80a5-7e3c1c52791d","Title":"Obamacare News & Topics | Entrepreneur.com","Description":"The latest news, videos, and discussion topics on Obamacare","DisplayUrl":"www.entrepreneur.com/topic/obamacare","Url":"http://www.entrepreneur.com/topic/obamacare"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=48&$top=1","type":"WebResult"},"ID":"ea29cd19-8d62-4a9c-be07-93daf85229ee","Title":"Keyword: obamacare","Description":"Too smart to be President. Is that really Barack Obama\u0027s problem? Mika Brzezinski thinks so. On today\u0027s Morning Joe, as Mike Barnicle and John Heilemann kicked around ...","DisplayUrl":"www.freerepublic.com/tag/obamacare/index","Url":"http://www.freerepublic.com/tag/obamacare/index"},{"__metadata":{"uri":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=49&$top=1","type":"WebResult"},"ID":"d50c2c5e-4bb6-47da-831d-1d0db4f436ef","Title":"Obamacare - Salon.com","Description":"Reagan\u0027s Southern strategy gave rise to the Tea Party Lawrence Freedman; I don\u0027t stand with Russell Brand, and neither should you Natasha Lennard","DisplayUrl":"www.salon.com/topic/obamacare","Url":"http://www.salon.com/topic/obamacare/"}],"__next":"https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=\u0027Obamacare\u0027&$skip=50"}}';


$response =json_decode($response);

$results = $response->d->results;

//var_dump($results);
//if no articles found, return false
if (count($results) < 0 ){
	return false;
}
//Get URL of first search term

if (!isset($results[0]->Url)){
	return false;
}else{
	$url = $results[0]->Url;
}

//make an alchemyDump
generateAlchemyDump($url);

//make unique filename
$output = urlencode(md5(time('ru').mt_rand())).".xml";

//make a raw text dump (each paragraph is an JSON array element)
scrapeAndSave($url,$output);


return $output;
}

function searchByTerm($searchTerm){
	//the bingSearchAndScrape function urlencodes the search terms, so we don't do it here
	$newsOutlets = array("FoxNews"=>"www.foxnews.com",
						 "Aljazeera"=>"www.aljazeera.com",
						 "CNN"=>"www.cnn.com",
						 "BBC"=>"www.bbc.co.uk",
						 "RT"=>"www.rt.com");
						 
	$scrapeResults = array();
	
	//foreach newsOutlet...
	foreach($newsOutlets as $name=>$url){
		
		//bing, search and scrape the site, and collect the file of the dump.
		$scrapeResult = bingSearchAndScrape($searchTerm,$url);
		//if there are URL results, store it in an array to be sent to the java
		
		if ($scrapeResult != false){
			$scrapeResults[] = $scrapeResult;
		}
	}
	//var_dump($scrapeResults);
	return $scrapeResults;

}
/*
function summary($outputs){
		foreach($outputs as $output){
			$text = json_decode(file_get_contents('temp/'.$output));
			
			$paragraphs = $text->paragraphs;
			$firstParaSentences = explode('.',trim($paragraphs[2]));
			$lastParaSentences = explode('.',trim($paragraphs[count($paragraphs)-1]));
			
			var_dump($firstParaSentences);
			if (isset($firstParaSentences[0])){
				$summary = $firstParaSentences[0];
			}
			if (isset($firstParaSentences[1])){
				$summary = $summary." ".$firstParaSentences[1];
			}
			if (isset($firstParaSentences[2])){
				$summary = $summary." ".$firstParaSentences[2];
			}
			if (isset($firstParaSentences[count($firstParaSentences)-1])&&count($firstParaSentences)-1 != 2&&count($firstParaSentences)-1 != 1){
				$summary = $summary." ".$firstParaSentences[count($firstParaSentences)-1];
			}
			
			echo $summary." ";
		
			
		}
}
*/
$searchTerm = $_POST['searchTerm'];

//Is the searchTerm a URL?
$reg_exUrl = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
if(preg_match($reg_exUrl, $searchTerm)){

	$file_headers = @get_headers($searchTerm);
	
	//if the URL doesn't exist, shit hits the fan and we return an error
	if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
		echo json_encode($return = array("status"=>"fail","message"=>"Sorry, that URL doesn't seem to exist (Error 404)") );
		exit();
	}
	
	//Use the AlchemyAPI to get 6 keywords of the article, so we can search other news outlets
	require_once 'lib/php/alchemyapi.php';
    $alchemyapi = new AlchemyAPI();
	
	$responseKeywords = $alchemyapi->keywords('url', $searchTerm, array("maxRetrieve"=>4));

	$keywords = "";
	
	foreach($responseKeywords["keywords"] as $keyword){
		$keywords = $keywords." ".$keyword["text"];
	}
	
	
	
	$outputs = searchByTerm($keywords);
	if (count($outputs)< 1){
		echo json_encode($return = array("status"=>"warning","message"=>"No Articles Found") );
	}else{
	
		echo json_encode($return = array("status"=>"success","message"=>"URL Aricle Search Commenced!") );
	}
	
	//exec("java ");
	
	
//If it isn't a URL, they are search terms
}else{
	
	//searchByTerm makes a bunch of scrape files for the Java code to handle :D
	$outputs = searchByTerm($keywords);
	if (count($outputs)< 1){
		echo json_encode($return = array("status"=>"warning","message"=>"No Articles Found") );
	}else{
		
		echo json_encode($return = array("status"=>"success","message"=>"URL Aricle Search Commenced!") );
	}
	echo json_encode($return = array("status"=>"success","message"=>"Aricle Term Search Commenced!") );

	
	
	
}

?>
