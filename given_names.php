<?php

$name = "";
if(isset($_GET["name"])){
	$name = $_GET["name"];
}

if(strlen($name)>1){
	// Create a stream
	$opts = [
		"http" => [
			"method" => "GET",
			"header" => "Accept: application/sparql-results+json"
		]
	];
	ini_set( 'user_agent', "OpenRefine_Proxy/1.3 (d.fichtmueller@bgbm.org) custom-php-script/0.1");
	$query = "https://query.wikidata.org/sparql?query=SELECT%20%3Fname%20%3FnameLabel%20%3Fgender%20%3Fcount%20WHERE%20%7B%0A%20%20%7B%0A%20%20%20%20SELECT%20%3Fname%20%3Fgender%20(COUNT(%3Fperson)%20as%20%3Fcount)%20WHERE%20%7B%0A%20%20%20%20%20%20BIND(".urlencode($name)."%20as%20%3FnameString).%0A%20%20%20%20%20%20%3Fname%20wdt%3AP31%2Fwdt%3AP279*%20wd%3AQ202444.%0A%20%20%20%20%20%20%3Fname%20wdt%3AP1705%20%3FnameString.%0A%20%20%20%20%20%20%3Fperson%20wdt%3AP735%20%3Fname.%0A%20%20%20%20%20%20%7B%0A%20%20%20%20%20%20%20%20%7B%0A%20%20%20%20%20%20%20%20%20%20%23male%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ6581097.%0A%20%20%20%20%20%20%20%20%7DUNION%7B%0A%20%20%20%20%20%20%20%20%20%20%23cisgender%20male%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ15145778.%0A%20%20%20%20%20%20%20%20%7DUNION%7B%0A%20%20%20%20%20%20%20%20%20%20%23trans%20man%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ2449503.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20BIND(%22male%22%20as%20%3Fgender).%0A%20%20%20%20%20%20%7DUNION%7B%0A%20%20%20%20%20%20%20%20%7B%0A%20%20%20%20%20%20%20%20%20%20%23female%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ6581072.%0A%20%20%20%20%20%20%20%20%7DUNION%7B%0A%20%20%20%20%20%20%20%20%20%20%23cisgender%20female%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ15145779.%0A%20%20%20%20%20%20%20%20%7DUNION%7B%0A%20%20%20%20%20%20%20%20%20%20%23trans%20woman%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ1052281.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20BIND(%22female%22%20as%20%3Fgender).%0A%20%20%20%20%20%20%7DUNION%7B%0A%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20%5B%5D.%0A%20%20%20%20%20%20%20%20MINUS%7B%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ6581097.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20MINUS%7B%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ15145778.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20MINUS%7B%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ2449503.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20MINUS%7B%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ6581072.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20MINUS%7B%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ15145779.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20MINUS%7B%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ1052281.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20BIND(%22other%22%20as%20%3Fgender).%0A%20%20%20%20%20%20%7DUNION%7B%0A%20%20%20%20%20%20%20%20FILTER%20NOT%20EXISTS%20%7B%3Fperson%20wdt%3AP21%20%5B%5D.%7D%0A%20%20%20%20%20%20%20%20BIND(%22unspecified%22%20as%20%3Fgender).%0A%20%20%20%20%20%20%7D%0A%20%20%20%20%7DGROUP%20BY%20%3Fname%20%3Fgender%0A%20%20%7D%0A%20%20%0A%20%20SERVICE%20wikibase%3Alabel%20%7B%20bd%3AserviceParam%20wikibase%3Alanguage%20%22en%2Cen%22.%20%7D%0A%7DORDER%20BY%20DESC(%3Fcount)";
	// DOCS: https://www.php.net/manual/en/function.stream-context-create.php
	$context = stream_context_create($opts);
	$content = file_get_contents($query, false, $context);
	
	$data = json_decode($content, true);
	
	//$count = count($data["results"]["bindings"]);
	//echo $query;
	//echo $data
	$count = array();
	$count["male"] = 0;
	$count["female"] = 0;
	$count["other"] = 0;
	$count["unspecified"] = 0;
	
	foreach($data["results"]["bindings"] as $result) { 
		$gender = $result["gender"]["value"]; 
		$countResult = $result["count"]["value"];
		$count[$gender]=$count[$gender]+$countResult;
	}
	$result = $count["male"]."|".$count["female"]."|".$count["other"]."|".$count["unspecified"];
	file_put_contents("given_names_results.txt", $name."\t".date("Y-m-d\TH:i:s", time())."\t".$result."\n", FILE_APPEND);
	echo $result;
}

 ?>