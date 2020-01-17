<?php

/*

mk2 | Mk2Gen

Class for starting the core library of PHP framework mk2 (provisional).

Copylight(C) Nakajima Satoru 2020.

*/

namespace mk2\core;

class Mk2Gen{

	# constructor

	public function __construct(){

		#default const setting
		$this->setConst();

		try{

			$this->loadingLibs();
			$this->loadingApps();
			$this->loadingLibsCustom();
			$passed=$this->routings();

			if(!empty($passed)){
				return;
			}

			if(Request::$params["routeType"]=="controller"){
				$this->setController();
			}
			else if(Request::$params["routeType"]=="render"){
				$this->setRender();
			}
			else
			{
				$this->setNotFound();
			}

		}catch(\Exception $e){
			$this->errorLogic($e);
		}catch(\Error $e){
			$this->errorLogic($e);
		}

		unset($this->routes);
		unset($this->Routing);

	}

	# (private) setConst
	private function setConst(){

		if(!defined('SYSNAME')){
			define("SYSNAME","AP1");
		}
		if(!defined('MK2_ROOT_LEVEL')){
			define("MK2_ROOT_LEVEL",1);
		}
		if(!defined('MK2_PATH_ROOTREVERSE')){
			define("MK2_PATH_ROOTREVERSE","../");
		}
		if(!defined('MK2_PATH_VENDOR')){
			define("MK2_PATH_VENDOR",MK2_PATH_ROOTREVERSE."../../vendor/");
		}
		if(!defined('MK2_PATH_APP')){
			define("MK2_PATH_APP",MK2_PATH_ROOTREVERSE."../../apps/".SYSNAME."/");
		}
		if(!defined('MK2_NAMESPACE')){
			define('MK2_NAMESPACE','mk2\core');
		}		
		if(!defined('MK2_PATH_APPCONF')){
			define("MK2_PATH_APPCONF",MK2_PATH_APP."AppConf/");
		}
		if(!defined('MK2_PATH_APPCONFINIT')){
			define("MK2_PATH_APPCONFINIT",MK2_PATH_APPCONF."Init/");
		}
		if(!defined('MK2_PATH_WEB')){
			define("MK2_PATH_WEB",MK2_PATH_APP."Web/");
		}
		if(!defined('MK2_PATH_CONF')){
			define("MK2_PATH_CONF",MK2_PATH_APPCONF."config.php");
		}
		if(!defined('MK2_PATH_BACKEND')){
			define("MK2_PATH_BACKEND","Backend");
		}
		if(!defined('MK2_PATH_MIDDLE')){
			define("MK2_PATH_MIDDLE","Middle");
		}
		if(!defined('MK2_PATH_RENDERING')){
			define("MK2_PATH_RENDERING","Rendering");
		}
		if(!defined("MK2_PATH_APP_CONTROLLER")){
			define("MK2_PATH_APP_CONTROLLER",MK2_PATH_APP.MK2_PATH_BACKEND."/Controller/");
		}
		if(!defined("MK2_PATH_APP_PACKER")){
			define("MK2_PATH_APP_PACKER",MK2_PATH_APP.MK2_PATH_BACKEND."/Packer/");
		}
		if(!defined("MK2_PATH_APP_SHELL")){
			define("MK2_PATH_APP_SHELL",MK2_PATH_APP.MK2_PATH_BACKEND."/Shell/");
		}
		if(!defined("MK2_PATH_APP_MODEL")){
			define("MK2_PATH_APP_MODEL",MK2_PATH_APP.MK2_PATH_MIDDLE."/Model/");
		}
		if(!defined("MK2_PATH_APP_TABLE")){
			define("MK2_PATH_APP_TABLE",MK2_PATH_APP.MK2_PATH_MIDDLE."/Table/");
		}
		if(!defined("MK2_PATH_APP_VALIDATOR")){
			define("MK2_PATH_APP_VALIDATOR",MK2_PATH_APP.MK2_PATH_MIDDLE."/Validator/");
		}
		if(!defined("MK2_PATH_APP_RENDER")){
			define("MK2_PATH_APP_RENDER",MK2_PATH_APP.MK2_PATH_RENDERING."/Render/");
		}
		if(!defined("MK2_PATH_APP_TEMPLATE")){
			define("MK2_PATH_APP_TEMPLATE",MK2_PATH_APP.MK2_PATH_RENDERING."/Template/");
		}
		if(!defined("MK2_PATH_APP_VIEWPART")){
			define("MK2_PATH_APP_VIEWPART",MK2_PATH_APP.MK2_PATH_RENDERING."/ViewPart/");
		}
		if(!defined("MK2_RENDERING_EXTENSION")){
			define("MK2_RENDERING_EXTENSION",".view");
		}

	}

	# (private) loading Libraries

	private function loadingLibs(){

		# Include basic core libraries.

		include("bin/function.php");
		include("bin/CR.php");
		include("bin/Routing.php");
		include("bin/Import.php");
		include("bin/CoreBlock.php");

		$this->Routing=new Routing();
	}

	# (private) loading Apps

	private function loadingApps(){

		# config file Check
		$configExistCheck=true;
		if(!file_exists(MK2_PATH_CONF)){

			$configExistCheck=false;
		}

		# config file exist check..
		if(!$configExistCheck){
			http_response_code(500);
			throw new \Exception('[SETTING ERROR] The following configuration file does not exist.'."\n".' PATH : "'.MK2_PATH_CONF.'"');
		}

		# Config FIle Include
		Config::set(include(MK2_PATH_CONF));

		# option include
		if(!empty(Config::get("optionInclude"))){
			$includes=Config::get("optionInclude");
			foreach($includes as $o_){
				if(file_exists(MK2_PATH_APP.$o_)){
					include(MK2_PATH_APP.$o_);
				}
			}
		}

		if(empty(Config::get("routing"))){
			http_response_code(500);
			throw new \Exception("[ROUTING ERROR] Routing information is not set.");
		}
		$this->routes=Config::get("routing");

	}

	# (private) loading Libraries custom

	private function loadingLibsCustom(){

		# get Use Class
		$useClass=Config::get("useClass");

		foreach($useClass as $className=>$u_){

			if($className=="Render"){
				$templateEngine=Config::get("templateEngine");

				if($templateEngine){

					if($templateEngine=="Smarty"){
						# if Smarty..
						include_once("bin/Render-of-Smarty.php");
					}
					else if($templateEngine=="Twig"){
						# if Twig..
						include_once("bin/Render-of-Twig.php");
					}
					else
					{
						throw new \Exception('"'.$templateEngine.'" is an unsupported template engine.');
					}
				}
				else
				{
					include_once("bin/".$className.".php");
				}
			}
			else
			{
				include_once("bin/".$className.".php");
			}
		}

	}

	# (private) routings

	private function routings(){

		if(empty($this->routes)){
			$this->routes=[];
		}
		$this->Routing->addRouting($this->routes);
		$passed=$this->Routing->check([
			"fullPath"=>Config::get("fullPath"),
			"routeMode"=>Config::get("routeMode"),
			"defControls"=>Config::get("defControls"),
		]);

		if(!empty($passed)){
			return true;
		}

	}

	# (parivate) controllerCheckExisted

	private function controllerCheckExisted(){

		$cont_jugement=false;

		$cont_url=MK2_PATH_APP_CONTROLLER.ucfirst(Request::$params["controller"])."Controller.php";

		if(!empty(file_exists($cont_url))){
			$cont_jugement=true;
		}
		else
		{
			if(Config::get("allowDirectory")){
				$allow_dir=Config::get("allowDirectory");

				if(!empty($allow_dir["Controller"])){
					foreach($allow_dir["Controller"] as $a_){
						$cont_url=MK2_PATH_APP_CONTROLLER.$a_."/".ucfirst(Request::$params["controller"])."Controller.php";
						if(!empty(file_exists($cont_url))){
							$cont_jugement=true;
							break;
						}
					}
				}
			}
		}

		if($cont_jugement){
			return $cont_url;
		}
		else
		{
			return null;
		}
	}

	# (private) controllerCheckModifier

	private function controllerCheckModifier($cont,$cont_name,$cont_url){

		$ignoreList=[
			"__construct",
			"setting",
			"setModel",
			"setPacker",
			"setValidator",
			"setController",
			"setShell",
			"getUrl",
			"redirect",
			"getRender",
			"getViewPart",
			"existRender",
			"existViewPart",
			"existTemplate",
			"set",
		];

		if(in_array(Request::$params["action"],$ignoreList)){
			$errText='Access error: The "'.Request::$params["action"].'" action is prepared as a special method, so it cannot access the "'.Request::$params["action"].'" method of "'.$cont_name.'".'."\n";
			$errText.='Specify an action name different from "'.Request::$params["action"].'" or adjust the routing settings.'."\n";
			$errText.= 'Path : '.$cont_url."\n\n";

			http_response_code(404);
			throw new \Exception($errText);
		}

		# if action of controller not existed,output error message.
		if(empty(method_exists($cont,Request::$params["action"]))){

			$errText= '"'.Request::$params["action"].'" method does not exist in "'.$cont_name.'".'."\n";
			$errText.= 'Create an "'.Request::$params["action"].'" method on the "'.$cont_name.'" controller or change the permissions to public.'."\n";
			$errText.= 'Path : '.$cont_url."\n\n";

			http_response_code(404);
			throw new \Exception($errText);
		}

		$check_method=new \ReflectionMethod($cont_name, Request::$params["action"]);
		$method_data=\Reflection::getModifierNames($check_method->getModifiers());

		# Error message output if the access modifier of the action is not public.
		if($method_data[0]!="public"){

			$errText= 'Access Error : "'.Request::$params["action"].'" method of "'.$cont_name.'" can not be accessed because the access modifier is not public.'."\n";
			$errText.= 'Please check the access modifier of the "'.Request::$params["action"].'" method again.'."\n";
			$errText.= 'Path : '.$cont_url."\n\n";

			http_response_code(404);
			throw new \Exception($errText);
		}

	}

	# (private) setController

	private function setController(){

		# Controller File Exist Check
		$cont_url=$this->controllerCheckExisted();

		# if controller enabled jugement not empty, output error message.
		if(!$cont_url){

			$errText=ucfirst(Request::$params["controller"]).'Controller.php" not Found.'."\n";
			$errText.='Please check whether the file of the "'.ucfirst(Request::$params["controller"]).'_Controller.php" controller exists in the directory below or inheriting the "'.ucfirst(Request::$params["controller"]).'" class from Controller. '."\n\n";

			http_response_code(404);
			throw new \Exception($errText);
		}

		$cont_name=MK2_NAMESPACE."\\".ucfirst(Request::$params["controller"])."Controller";
		include($cont_url);

		# if controller class not empty, output error message.
		if(empty(class_exists($cont_name))){

			$errText='"'.$cont_name.'" class does not exist in "'.$cont_name.'.php".'."\n";
			$errText.='Please declare the "'.$cont_name.'" class to "'.$cont_name.'.php" file.'."\n";
			$errText.='Path : '.$cont_url."\n\n";

			http_response_code(404);

			throw new \Exception($errText);
		}

		# controller class constructor.
		$cont=new $cont_name();

		# action of controller modifier check
		$this->controllerCheckModifier($cont,$cont_name,$cont_url);

		# filter before hook.
		#$cont->filterBefore();

		# Execution of action method.
		if(!empty(Request::$params["request"])){
			Request::$params["request"]=array_values(Request::$params["request"]);
			$out=$cont->{Request::$params["action"]}(...Request::$params["request"]);
		}
		else
		{
			$out=$cont->{Request::$params["action"]}();
		}

		# rendering..
		$cont->___rendering($out);
		#$cont->filterAfter();

	}

	# (private) renderCheckExisted

	private function renderCheckExisted(){

		$render_jugement=false;

		$render_url=MK2_PATH_APP_RENDER.Request::$params["render"].MK2_RENDERING_EXTENSION;

		if(!empty(file_exists($render_url))){
			return $render_url;
		}
	}

	# (private) setRender

	private function setRender(){

		# render File Exist Check
		$render_url=$this->renderCheckExisted();

		# if controller enabled jugement not empty, output error message.
		if(!$render_url){

			$errText='"'.Request::$params["render"].'" not Found.'."\n";

			http_response_code(404);
			throw new \Exception($errText);
		}

		include($render_url);

	}

	# (private) setNotFound

	private function setNotFound(){

		http_response_code(404);
		throw new \Exception("[Page not Found] Please check if the following address is configured in routing.\nPath : ".Request::$params["url"]."\n\n");

	}

	# (private) errorLogic

	private function errorLogic($errMsg,$mode=null){

		if(http_response_code()==200){
			http_response_code(500);
		}

		$this->Routing->error(http_response_code(),$errMsg);

		try{

			Request::$params["request"]=[$errMsg];

			if(!empty(Request::$params["routeType"])){
				
				if(Request::$params["routeType"]=="controller"){
					$this->setController();
				}
				else if(Request::$params["routeType"]=="render"){
					$this->setRender();
				}
			
			}
			else
			{
				
				echo $errMsg;
	
			}

		}catch(\Exception $e){
			echo $e;
		}

	}
}