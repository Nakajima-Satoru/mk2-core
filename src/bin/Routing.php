<?php

/**
 * 
 * mk2 Routing Class
 *  
 * A core library for routing.
 * 
 * @copyright	 Copyright (C) Nakajima Satoru. 
 * @link		 https://www.mk2-php.com/
 * 
 */

namespace mk2\core;

class Routing{

	private $routes=[];
	private $defControls=[
		"controller"=>"main",
		"action"=>"index",
	];
	private $routeMode="auto";
	private $fullPath=false;
	private $queryArea="";

	# addRouting

	public function addRouting($params){

		# scope on convert
		if(!empty($params["scope"])){
			if(!empty($params["pages"])){

				$pagesList=$params["pages"];
				$params["pages"]=[];
		
				foreach($pagesList as $aliasName=>$p_){
					if($aliasName=="/"){ $aliasName=""; }
					foreach($p_ as $url=>$pp_){
						if($url=="/"){ $url=""; }
						$url=$aliasName.$url;
						$params["pages"][$url]=$pp_;
					}
				}
			}
		}

		if(!empty($params["pages"])){
			foreach($params["pages"] as $url=>$p_){
				# convert route param
				$params["pages"][$url]=$this->convertRouteParam($p_);
			}
		}

		# error
		if(!empty($params["error"])){
			foreach($params["error"] as $code=>$p_){
				# convert route param(error)
				if(!empty($params["errorScope"])){
					foreach($p_ as $code2=>$pp_){
						$params["error"][$code][$code2]=$this->convertRouteParam($pp_);
					}
				}
				else{
					$params["error"][$code]=$this->convertRouteParam($p_);
				}
			}
		}

		$this->routes=$params;
	}

	# check

	public function check($option=[]){

		if(!empty($option["fullPath"])){
			$this->fullPath=$option["fullPath"];
		}

		if(!empty($option["defControls"])){
			$this->setDefControls($option["defControls"]);
		}

		if(!empty($option["routeMode"])){
			$this->routeMode=$option["routeMode"];
		}
		
		$this->defaultCheck();
		$passed=$this->customCheck();
		$this->getRequestData();

		Request::$params=$this->params;

		if($passed){
			return true;
		}

	}

	# error

	public function error($errCode,$Err){

		$beforeRequest=Request::$params;

		if(!empty($this->routes["errorScope"])){

			if(!empty($this->routes["error"]["/"][$errCode])){
				$errRoute=$this->routes["error"]["/"][$errCode];
			}
			else if(!empty($this->routes["error"]["/"][null])){
				$errRoute=$this->routes["error"]["/"][null];
			}

			foreach($this->routes["error"] as $url=>$ers){
				$urls=explode("/",$url);
				if(empty($urls[1])){ $urls[1]=""; }
				$bases=explode("/",$beforeRequest["base"]);
				if(empty($bases[1])){ $bases[1]=""; }
				if($urls[1]==$bases[1]){
					if(!empty($ers[$errCode])){
						$errRoute=$ers[$errCode];
					}
					else if(!empty($ers[null])){
						$errRoute=$ers[null];
					}
					break;
				}
			}

		}
		else
		{
			if(!empty($this->routes["error"][$errCode])){
				$errRoute=$this->routes["error"][$errCode];
			}
			else if(!empty($this->routes["error"][null])){
				$errRoute=$this->routes["error"][null];
			}
		}

		if(!empty($errRoute)){

			if(is_callable($errRoute)){

				Request::$params=$this->params;

				// default responseHeader setting
				$defHeader=Config::get("defHeader");

				if(!empty($defHeader)){
					foreach($defHeader as $k_=>$v_){
						@header($k_.": ".$v_);
					}
				}

				$out=call_user_func($errRoute,$Err);
				echo $out;
				die;
			}
			else
			{
				$this->params=array_merge($this->params,$errRoute);
			}
		}
		else
		{
			$this->params=[];
		}

		Request::$params=$this->params;
		if(!empty($beforeRequest["namespace"])){
			Request::$params["namespace"]=$beforeRequest["namespace"];
		}
		if(!empty($beforeRequest)){
			Request::$params["beforeRequest"]=$beforeRequest;
		}

	}

	# (private) defaultCheck

	private function defaultCheck(){

		$phpSelf=dirname($_SERVER["PHP_SELF"]);
		for($s1=0;$s1<MK2_ROOT_LEVEL;$s1++){
			$phpSelf=dirname($phpSelf);
		}
		$phpSelf=str_replace("\\","/",$phpSelf);
		if($phpSelf=="/"){
			$phpSelf="";
		}
		$requestUrl=$_SERVER["REQUEST_URI"];

		$fullPath="";
		if($this->fullPath){
			if(empty($_SERVER["HTTPS"])){
				$protocol="http://";
			}
			else
			{
				$protocol="https://";
			}
			$fullPath=$protocol.$_SERVER["HTTP_HOST"];
		}

		$this->params["url"]=$fullPath.$requestUrl;
		$this->params["root"]=$fullPath.$phpSelf."/";
		$this->params["base"]=str_replace($this->params["root"],"/",$this->params["url"]);
		$this->params["domain"]=$_SERVER["HTTP_HOST"];
		$this->params["option"]=[
			"method"=>$_SERVER["REQUEST_METHOD"],
			"port"=>$_SERVER["SERVER_PORT"],
			"remote"=>$_SERVER["REMOTE_ADDR"],
		];

		// ELB IP address exists...
		if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
			$this->params["option"]["remote"]=$_SERVER["HTTP_X_FORWARDED_FOR"];
		}

		$queryArea=$requestUrl;
		if($phpSelf){
			$queryArea=str_replace($phpSelf."/","",$requestUrl);
		}
		$queryArea=explode("?",$queryArea);
		$queryArea=explode("/",$queryArea[0]);

		if(!$queryArea[0]){
			array_shift($queryArea);
		}
		if(empty($queryArea[count($queryArea)-1])){
			unset($queryArea[count($queryArea)-1]);
		}
		$this->queryArea=$queryArea;

		if($this->routeMode=="auto"){

			$this->params["routeType"]="controller";

			if(!empty($this->defControls["controller"])){
				$this->params["controller"]=$this->defControls["controller"];
			}
			if(!empty($this->defControls["action"])){
				$this->params["action"]=$this->defControls["action"];
			}

			if(!empty($queryArea[0])){
				$this->params["controller"]=$queryArea[0];
			}

			if(!empty($queryArea[1])){
				$this->params["action"]=$queryArea[1];
			}

			if(count($queryArea)>2){

				$this->params["request"]=[];
				foreach($queryArea as $ind=>$qa){
					if($ind>1){
						$this->params["request"][]=$qa;
					}
				}
			}
		}
	}

	# (private) customCheck

	private function customCheck(){

		$scheduleRouting=null;

//		$match_pages=[];
//		$match_queryArea=[];

		foreach($this->routes["pages"] as $url=>$rp){

			if(gettype($rp)=="object"){
				$rp=[
					"callback"=>$rp,
				];
			}

			if($url=="/"){
				$url="";
			}

			// global aregment set
			$anyAregment="{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}";
			$url=str_replace("*",$anyAregment,$url);

			$qa=$this->queryArea;

			$urld=explode("/",$url);
			array_shift($urld);

			$enabled_pages=true;

			foreach($qa as $ind=>$q){

				if(!empty($urld[$ind])){

					if(strpos($urld[$ind],"{:")>-1){

						if(empty($rp["request"])){
							$rp["request"]=[];
						}
						$rp["request"][]=$q;

						$q=$urld[$ind];
					}

					if($q!=$urld[$ind]){
						$enabled_pages=false;
					}

				}
				else
				{
					$enabled_pages=false;
				}
			}

//			$match_pages[]=$enabled_pages;

			$enabled_queryArea=true;
			foreach($urld as $ind=>$u){
				if(!empty($qa[$ind])){

					if(strpos($u,"{:")>-1){
						$qa[$ind]=$u;
					}

					if($u!=$qa[$ind]){
						$enabled_queryArea=false;
					}
				}
				else
				{
					if(!(strpos($u,"{:")>-1 && strpos($u,"?}")>-1)){
						$enabled_queryArea=false;
					}
				}
			}

//			$match_queryArea[]=$enabled_queryArea;

			if($enabled_pages && $enabled_queryArea){

				$enabled=true;
				if(is_array($rp)){
					if(!empty($rp["method"])){
						if(mb_strtolower($rp["method"])!=mb_strtolower($_SERVER["REQUEST_METHOD"])){
							$enabled=false;
						}
					}
				}

				if($enabled){
					$scheduleRouting=[
						"_url"=>$url,
						"route"=>$rp,
					];
				}
			}
		}

//		debug($match_pages);
//		debug($match_queryArea);

		if($scheduleRouting){
			if(!empty($scheduleRouting["route"]["callback"])){

				Request::$params=$this->params;

				// default responseHeader setting
				$defHeader=Config::get("defHeader");

				if(!empty($defHeader)){
					foreach($defHeader as $k_=>$v_){
						@header($k_.": ".$v_);
					}
				}

				if(!empty($scheduleRouting["route"]["request"])){
					$output=call_user_func($scheduleRouting["route"]["callback"],...$scheduleRouting["route"]["request"]);
				}
				else
				{
					$output=call_user_func($scheduleRouting["route"]["callback"]);
				}
				echo $output;
				return true;
			}
			else
			{
				$this->params=array_merge($this->params,$scheduleRouting["route"]);
			}
		}
		else
		{
			if(empty($this->params["routeType"])){
				$this->params["routeType"]="notFound";
			}
		}

		return;
	}

	# (private) setDefControls

	private function setDefControls($params){
		if(!empty($params)){
			if(is_array($params)){
				$this->defControls=$params;
			}
			else
			{
				$spn=explode("@",$params);
				$this->defControls["controller"]=$spn[0];

				if(!empty($spn[1])){
					$this->defControls["action"]=$spn[1];
				}
			}
		}
	}

	# (private) getRequestData

	private function getRequestData(){

		//Get params

		Request::$get=$_GET;

		if(!empty($this->params["query"])){
			Request::$get=$this->params["query"];
		}

		//POST params

		Request::$post=$_POST;

		if(!empty($this->params["post"])){
			Request::$post=$this->params["post"];
		}

		if($_FILES){
			foreach($_FILES as $key=>$f_){
				if(is_array(@$f_["name"])){
					foreach($f_ as $keyname=>$ff_){
						foreach($ff_ as $index=>$fff_){
							Request::$post[$key][$index][$keyname]=$fff_;
						}
					}
				}
				else
				{
					Request::$post[$key]=$f_;
				}
			}
		}

		# JSON Data
		$header=getallheaders();
		if(!empty($header["Content-Type"])){
			if($header["Content-Type"]=="application/json"){
				# get JSON DATA
				$json=file_get_contents("php://input");
				$json=json_decode($json,true);
				Request::$json=$json;
			}
		}
	}

	# (private) convertRouteParam

	private function convertRouteParam($param){

		if(is_callable($param)){
			$buff=$param;
		}
		else if(!is_array($param)){
			$param=explode("@",$param);
			$buff=[];
			if(!empty($param[1])){
				# type :controller
				$buff["routeType"]="controller";

				if(strpos($param[0],"\\")){
					$param[0]=explode("\\",$param[0]);
					$contBuff=$param[0][count($param[0])-1];
					$namespace="";
					for($u1=0;$u1<count($param[0])-1;$u1++){
						if($u1!=0){
							$namespace.="\\";
						}
						$namespace.=$param[0][$u1];
					}
					$buff["namespace"]=$namespace;
					$param[0]=$contBuff;
				}
				else{
					$buff["namespace"]="";
				}

				$buff["controller"]=$param[0];
				$param[1]=explode(":",$param[1]);
				$buff["action"]=$param[1][0];

				if(count($param[1])>1){
					$buff["request"]=[];
					foreach($param[1] as $ind=>$pp_){
						if($ind>=1){
							$buff["request"][]=$pp_;
						}
					}
				}
			}
			else
			{
				# type :render
				$buff["routeType"]="render";
				$buff["render"]=$param[0];

				$param[0]=explode(":",$param[0]);	
				if(count($param[0])>1){
					$buff["request"]=[];
					foreach($param[0] as $ind=>$pp_){
						if($ind>=1){
							$buff["request"][]=$pp_;
						}
					}
				}

			}
		}
		else
		{
			$buff=$p_;
		}

		return $buff;
	}

}