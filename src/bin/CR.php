<?php
/*
mk2 | CR (Config / Request)

:Config
Class to set/get configuration information.

:Request
Stores request data such as POST and GET.

Copylight(C) Nakajima Satoru 2020.

*/

namespace mk2\core;

// Config
class Config{

	private static $data=[];

	# set

	public static function set($params){
		self::$data=$params;
	}

	# setDetail

	public static function setDetail($name,$params){
		self::$data[$name]=$params;
	}

	# get

	public static function get($name,$arg1=null){

		if($name=="database"){
			if(!empty(self::$data[$name])){
				if($arg1){
					if(!empty(self::$data[$name][$arg1])){
						return self::$data[$name][$arg1];
					}
				}
				else
				{
					return self::$data[$name];
				}
			}
		}
		else
		{
			if(!empty(self::$data[$name])){
				return self::$data[$name];
			}
		}
	}
}

// Requests
class Request{

	public static $get=[];
	public static $post=[];
	public static $params=[];
	public static $json=[];

	# getAll

	public static function getAll(){

		$out= new \stdClass();
		$out->params=self::$params;
		$out->get=self::$get;
		$out->post=self::$post;
		$out->json=self::$json;

		return $out;

	}
	
}