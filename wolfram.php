<?php


		function Parse ($url) {
        $fileContents= file_get_contents($url);
        $fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
        $fileContents = trim(str_replace('"', "'", $fileContents));
        $simpleXml = simplexml_load_string($fileContents);
        $json = json_encode($simpleXml);

        return $json;
		}
		
		$input = urlencode($_POST['input']);
	
	echo Parse("http://api.wolframalpha.com/v2/query?input=".$input."&appid=RJ45AJ-T8JV49E92P");


?>