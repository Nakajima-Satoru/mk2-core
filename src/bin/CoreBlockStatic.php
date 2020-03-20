<?php

namespace mk2\core;

class CoreBlockStatic{

	/**
	 * _addClassLoading
	 */
	public static function _addClassLoading($classType,$params,$addAllow=null){

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

		$allow_dir=Config::get("allowDirectory");
		$allowPathList=[
			$classPath,
		];
		if(!empty($addAllow)){
			foreach($addAllow as $a_){
				$allowPathList[]=$a_;
			}
		}
		if(!empty($allow_dir[$classType])){
			foreach($allow_dir[$classType] as $am){
				$allowPathList[]=$classPath."/".$am."/";
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

			$jugement=false;
			$enable_urls=[];
			foreach($allowPathList as $m_){

				$url=$m_.$className.$classType.".php";

				if(!empty(file_exists($url))){
					$enable_urls[]=$url;
					$jugement=true;
				}
			}

			if(!$jugement){
/*
				if(empty($this->{$classType})){
					$this->{$classType}=new \stdClass();
				}
*/
				$defaultClassName="mk2\core\\".$classType;
				$outputs->{$classType}->{$className}=new $defaultClassName();
				
			}
			else
			{
				foreach($enable_urls as $url){
					include_once($url);
				}

				# namespace check
				if(!empty(Request::$params["namespace"])){
					if(!empty($option["namespace"])){
						$path=$option["namespace"]."\\".$className.$classType;						
						unset($option["namespace"]);
					}
					else{
						$path=Request::$params["namespace"]."\\".$className.$classType;
					}

					if(!class_exists($path)){
						$path=MK2_NAMESPACE."\\".$className.$classType;
					}
				}
				else{
					$path=MK2_NAMESPACE."\\".$className.$classType;
				}

				if(!class_exists($path)){
					$path="mk2\core\\".$className.$classType;
				}
				if(!class_exists($path)){
					$path=$className.$classType;
				}

				if($classType=="Packer"){

					$classNameUseView=$className."UI";

					$pathUseView=MK2_NAMESPACE."\\".$className.$classType."UI";

					if(!class_exists($pathUseView)){
						$pathUseView="mk2\core\\".$className.$classType."UI";
					}
					if(!class_exists($pathUseView)){
						$pathUseView=$className.$classType."UI";
					}
				}

				if(class_exists($path)){
/*
					if(empty($this->{$classType})){
						$this->{$classType}=new \stdClass();
					}
*/
					$buffer=new $path($option);
					if($classType=="Table"){
						$buffer->_settingsModel();
					}

					if(!empty($outputClassName)){
						$outputs->{$classType}->{$outputClassName}=$buffer;
					}
					else{
						$outputs->{$classType}->{$className}=$buffer;
					}

				}

				if($classType=="Packer"){

					if(class_exists($pathUseView)){
						$classNameUseView=substr($classNameUseView,0,-2);
						$outputs->PackerUI->{$classNameUseView}=new $pathUseView($option);

					}
				}
			}
		}

		return $outputs;

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