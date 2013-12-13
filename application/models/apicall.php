<?php 
	public function getarray($url)
	{
 		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$values = json_decode($output,TRUE);
		return $values;
	}
?>	