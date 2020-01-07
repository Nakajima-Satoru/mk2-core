<?php
/*
mk2 | function

Commonly used functions.

Copylight(C) Nakajima Satoru 2020.

*/

# debug

function debug($data){

	$e = new Exception;
	$arys = $e->getTrace();

	if(php_sapi_name()=="cli"){
		print_r('[Debug : '.$arys[0]["file"]."(line ".$arys[0]["line"].")]");
		print_r("\n");
		print_r($data);
		print_r("\n");
	}
	else
	{
		echo '<div class="error-block">';
		print_r('<p style="font-weight:bold;">Debug : '.$arys[0]["file"]."(line ".$arys[0]["line"].")</p>");
		echo "<pre>";
		$val=print_r($data,true);
		$val=str_replace("<!--","&lt;--",$val);
		$val=str_replace("-->","--&gt;",$val);
		echo $val;
		echo "</pre>";
		echo "</div>";
	}
}

# sanitise

function sanitize($string,$mode="html"){

	if($mode=="html"){
		$string=htmlspecialchars($string);
	}
	else if($mode=="php"){
		
		$sanitize_code=array(
			"<?"=>"&lt;?",
			"?>"=>"?&gt;",
		);

		foreach($sanitize_code as $key=>$s_){
			$string=str_replace($key,$s_,$string);
		}
	}
	else if($mode=="script"){

		$sanitize_code=array(
			"<script"=>"&lt;script",
			"</script>"=>"&lt/script&gt;",
		);

		foreach($sanitize_code as $key=>$s_){
			$string=str_replace($key,$s_,$string);
		}

	}

	return $string;
}

# original json encode

function jsonEnc($params,$mode=true){

	if($mode){
		return json_encode($params,JSON_UNESCAPED_UNICODE);
	}
	else
	{
		return json_encode($params);
	}
}

# original json decode

function jsonDec($params,$mode=true){

	if($mode){
		return json_decode($params,true);
	}
	else
	{
		return json_decode($params);
	}

}