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
		
		if (isset($_POST['type'])){
			$type = $_POST['type'];
		}else{
			$type = "json";
		}
		
	if ($type == "json"){
		$return["name"] = $_POST['input'];
		$return["jsonCall"] = Parse("http://api.wolframalpha.com/v2/query?input=".$input."&appid=RJ45AJ-T8JV49E92P");
		echo json_encode($return);
		
	}else if ($type == "xml"){
		$return["name"] = $_POST['input'];
		$return["xmlCall"] = file_get_contents("http://api.wolframalpha.com/v2/query?input=".$input."&appid=RJ45AJ-T8JV49E92P");
		echo json_encode($return);
	}
	


?>