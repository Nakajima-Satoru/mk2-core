<?php

/*

mk2 | Import

Static class methods when each class requires preloading.

Copylight(C) Nakajima Satoru 2020.

*/

namespace mk2\core;

class Import{

	# Import Controller Class

	public static function Controller($name,$path=null){
		self::_import("Controller",$name,null,$path);
	}

	# Import Model Class

	public static function Model($name,$path=null){
		self::_import("Model",$name,null,$path);

	}

	# Import Table Class

	public static function Table($name,$path=null){
		self::_import("Table",$name,null,$path);

	}

	# Import Schema Class

	public static function Schema($name,$path=null){
		self::_import("Schema",$name,null,$path);

	}

	# Import Validator Class

	public static function Validator($name,$path=null){
		self::_import("Validator",$name,null,$path);

	}

	# Import Packer Class

	public static function Packer($name,$path=null){
		self::_import("Packer",$name,[MK2_PATH_VENDOR."mk2/packer/src/"],$path);

	}

	# Import Shell Class

	public static function Shell($name,$path=null){
		self::_import("Shell",$name,null,$path);

	}

	# Import Render Class

	public static function Render($name,$path=null){
		self::_import("Render",$name,null,$path);

	}

	# Import Plugin	Library

	public static function Plugin($path){

		$plugin_path=MK2_PATH_APP_PLUGIN.$path;

		if(!empty(file_exists($plugin_path))){
			include($plugin_path);
		}

	}

	# (private) _import

	private static function _import($className,$classFileName,$addAllow=null,$needPath=null){

		if(!is_array($classFileName)){
			$classFileName=[$classFileName];
		}

		foreach($classFileName as $c){

			// set allow Directory
			$allow_dir=Config::get("allowDirectory");

			if($needPath){
				$classPath=constant("MK2_PATH_APP_".strtoupper($className)).$needPath;
			}
			else{
				$classPath=constant("MK2_PATH_APP_".strtoupper($className));
			}

			$pathList=[
				$classPath,
			];
			if(!empty($addAllow)){
				foreach($addAllow as $a_){
					$pathList[]=$a_;
				}
			}
			if(!empty($allow_dir[$className])){
				foreach($allow_dir[$className] as $am){
					$pathList[]=$classPath.$am."/";
					$pathList[]=$am."/";
				}
			}

			// search...
			$enable_urls=[];
			foreach($pathList as $cm_){

				$url=$cm_.$c.$className.".php";

				if(!empty(file_exists($url))){
					if(empty(class_exists(basename($c.$className)))){
						include_once($url);
					}
				}
			}
/*
			if($jugement){
				if(empty(class_exists(basename($c.$className)))){
					include_once($url);
				}
			}
*/
		}
	}
}