<?php

/**
 * 
 * mk2 function
 * 
 * Commonly used functions.
 * 
 * @copyright	 Copyright (C) Nakajima Satoru. 
 * @link		 https://www.mk2-php.com/
 *  
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
		$val=sanitize($val);
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