<?php

namespace mk2\core;

class CoreBlockStatic{

	private const CORE_NAMESPACE="mk2\core\\";

	/**
	 * _addClassLoading
	 */
	public static function _addClassLoading(&$context,$classType,$params,$addAllow=null){

		$outputs=new \stdClass();

		$outputs->{$classType}=new \stdClass();

		if($classType=="Packer"){
			$outputs->PackerUI=new \stdClass();
		}

		$classPath=constant("MK2_PATH_APP_".strtoupper($classType));
		
		if(!is_array($params)){
			$params=[$params];
		}

		# Set allow directory

		$classConf=Config::get("class");
		if(empty($classConf[$classType]["enable"])){
			return;
		}

		$classConf=$classConf[$classType];
		if(!empty($classConf["allowDirectory"])){
			$allow_dir=$classConf["allowDirectory"];
		}

		$allowPathList=[
			$classPath,
		];
		if(!empty($addAllow)){
			foreach($addAllow as $a_){
				$allowPathList[]=$a_;
			}
		}
		if(!empty($allow_dir)){
			foreach($allow_dir as $am){
				$allowPathList[]=$classPath."/".$am."/";
				$allowPathList[]=$am."/";
			}
		}

		# serach class..

		foreach($params as $key=>$p_){
			$option=[];
			if(is_int($key)){
				if(gettype($p_)=="array"){
					$className=ucfirst(key($p_));
					$option=$p_;
				}
				else{
					$className=ucfirst($p_);
				}
			}
			else
			{
				$option=$p_;
				if(!empty($option["changeClass"])){
					$className=$option["changeClass"];
					$outputClassName=$key;
				}
				else{
					$className=ucfirst($key);
				}
			}

			# If Initialize file existe. Initialize data on marge.
			if(empty($option["_independent"])){
				$initPath=MK2_PATH_APPCONFINIT.$className.$classType."Init.php";
				if(file_exists($initPath)){
					$init=include($initPath);
					$option=array_merge($option,$init);
				}
			}
			else{
				unset($option["_independent"]);
			}

			$enable_urls=[];
			foreach($allowPathList as $m_){

				$url=$m_.$className.$classType.".php";

				if(!empty(file_exists($url))){
					$enable_urls[]=$url;
				}
			}

			if($enable_urls){
				foreach($enable_urls as $url){
					include_once($url);
				}
			}

			# namespace check
			if(!empty($option["namespace"])){
				$path=$option["namespace"]."\\".$className.$classType;
				unset($option["namespace"]);
			}
			else{
				if(!empty(Request::$params["namespace"])){
					$path=Request::$params["namespace"]."\\".$className.$classType;
				}
				else{
					$path=self::CORE_NAMESPACE.$className.$classType;
				}
			}

			if(!class_exists($path)){
				$path=MK2_NAMESPACE."\\".$className.$classType;
			}
			if(!class_exists($path)){
				$path=self::CORE_NAMESPACE.$className.$classType;
			}
			if(!class_exists($path)){
				if($classType=="Packer"){
					// Standard Packer Class..
					$path="mk2\packer\\".$className.$classType;
				}
			}
			if(!class_exists($path)){
				$path=$className.$classType;
			}

			if($classType=="Packer"){

				$classNameUseView=$className."UI";

				$pathUseView=MK2_NAMESPACE."\\".$className.$classType."UI";

				if(!class_exists($pathUseView)){
					$pathUseView=self::CORE_NAMESPACE.$className.$classType."UI";
				}
				if(!class_exists($pathUseView)){
					// Standard Packer Class..
					$pathUseView="mk2\packer\\".$className.$classType."UI";
				}
				if(!class_exists($pathUseView)){
					$pathUseView=$className.$classType."UI";
				}
			}


			if($classType=="Table"){
				if(!empty($outputClassName)){
					$option["table"]=lcfirst($ouptutClassName);
				}
				else
				{
					$option["table"]=lcfirst($className);
				}
			}

			if(class_exists($path)){

				$buffer=new $path($option);

				if(!empty($outputClassName)){
					$outputs->{$classType}->{$outputClassName}=$buffer;
				}
				else{
					$outputs->{$classType}->{$className}=$buffer;
				}

			}
			else{

				if(!empty($classConf["maintenance"])){

					$loadClassName=self::CORE_NAMESPACE.$classType;
					$buffer=new $loadClassName($option);

					if(!empty($outputClassName)){
						$outputs->{$classType}->{$outputClassName}=$buffer;
					}
					else{
						$outputs->{$classType}->{$className}=$buffer;
					}
				}

			}

			if($classType=="Packer"){

				if(class_exists($pathUseView)){
					$classNameUseView=substr($classNameUseView,0,-2);
					$outputs->PackerUI->{$classNameUseView}=new $pathUseView($option);
				}
			}
		}

		// context setting
		foreach($outputs->{$classType} as $className=>$o_){
			if(empty($context->{$classType})){
				$context->{$classType}=new \stdClass();
			}

			$context->{$classType}->{$className}=$o_;
		}

		if($classType=="Packer"){
			foreach($outputs->PackerUI as $className=>$o_){
				if(empty($context->PackerUI)){
					$context->PackerUI=new \stdClass();
				}
	
				$context->PackerUI->{$className}=$o_;
			}
		}

	}

	/**
	 * _getUrl
	 */
	public static function _getUrl($params){

		if(is_array($params)){

			$urla="";

			if(!empty($params["head"])){
				$urla.=$params["head"]."/";
				unset($params["head"]);
			}

			if(empty($params["controller"])){
				$params["controller"]=Request::$params["controller"];
			}

			if(empty($params["action"]) || @$params["action"]=="index"){
				$params["action"]="";
			}

			$urla.=$params["controller"];

			if($params["action"]){
				$action=$params["action"];
				$urla.="/".$action;
			}

			//get
			if(!empty($params["?"])){
				$get_params=$params["?"];
			}

			//hash
			if(!empty($params["#"])){
				$hash=$params["#"];
			}

			unset(
				$params["controller"],
				$params["action"],
				$params["?"],
				$params["#"]
			);

			if(!empty($params)){
				if(empty($action)){
					$urla.="/index";
				}

				foreach($params as $tq_){
					$urla.="/".$tq_;
				}
			}

			if(!empty($get_params)){
				if(is_array($get_params)){
					$get_str="?";
					$ind=0;
					foreach($get_params as $key=>$g_){
						if($ind>0){
							$get_str.="&";
						}
						$get_str.=$key."=".$g_;
						$ind++;
					}
				}
				else
				{
					$get_str="?".$get_params;
				}
				$urla.=$get_str;
			}
			if(!empty($hash)){
				$urla.="#".$hash;
			}

			//unset(memory suppression)
			unset($action);
			unset($get_params);
			unset($params);

			return Request::$params["root"].$urla;

		}
		else
		{
			if($params=="/"){
				return Request::$params["root"];
			}
			else
			{
				if($params[0]=="@"){
					return Request::$params["root"].substr($params,1);
				}
				else
				{
					return $params;
				}
			}
		}

	}

}