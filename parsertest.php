<pre>
<?php

	require_once 'lib/php/alchemyapi.php';
    $alchemyapi = new AlchemyAPI();
	
	$responseKeywords = $alchemyapi->keywords('url', "http://www.cnn.com/2013/11/08/us/fort-meade-sexual-abuse-investigation/index.html?hpt=hp_t2", array("maxRetrieve"=>6));

	$keywords = $responseKeywords["keywords"];
?>
</pre>