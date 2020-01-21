<?php

/*

mk2 | Mk2GenCLI

Photoshop library for mk2 command execution.

Copylight(C) Nakajima Satoru 2020.

*/

namespace mk2\core;

class Mk2GenCLI{

	public function __construct(){

		#default const setting
		$this->setConst();
		try{

			$this->loadingLibs();
			$this->loadingApps();
			$this->loadingLibsCustom();

			$argv=$_SERVER["argv"];
			array_shift($argv);

			$this->setShell($argv);

		}catch(\Exception $e){
			print_r($e);
		}
		
	}
	# (private) setConst
	private function setConst(){

		if(!defined('MK2_SYSNAME')){
			define("MK2_SYSNAME","AP1");
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
			define("MK2_PATH_APP",MK2_PATH_ROOTREVERSE."../../apps/".MK2_SYSNAME."/");
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
		if(!defined("MK2_PATH_APP_PLUGIN")){
			define("MK2_PATH_APP_PLUGIN",MK2_PATH_APP.MK2_PATH_RENDERING."/plugin/");
		}
		if(!defined("MK2_PATH_APP_VIEWPART")){
			define("MK2_PATH_APP_VIEWPART",MK2_PATH_APP.MK2_PATH_RENDERING."/ViewPart/");
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

	}

	# (private) loading Libraries custom

	private function loadingLibsCustom(){

		# get Use Class
		$useClass=Config::get("useClass");

		foreach($useClass as $className=>$u_){
			include_once("bin/".$className.".php");
		}

	}

	# (private) shellCheckModifier

	private function shellCheckModifier($shell,$shellAction,$shellClassName){

		$ignoreList=[
			"__construct",
			"setModel",
			"setTable",
			"setValidator",
			"setPacker",
			"setController",
			"setShell",
			"getUrl",
			"redirect",
			"getRender",
			"getViewPart",
			"existRender",
			"existViewPart",
			"existTemplate",
		];


		# if action of shell not existed,output error message.
		if(empty(method_exists($shell,$shellAction))){
			throw new \Exception('not Found "'.$shellClassName.'" Class on public "'.$shellAction.'" method.');
		}

		$check_method=new \ReflectionMethod($shellClassName, $shellAction);
		$method_data=\Reflection::getModifierNames($check_method->getModifiers());

		# Error message output if the access modifier of the action is not public.
		if($method_data[0]!="public"){
			throw new \Exception('"'.$shellClassName.'" Class on "'.$shellAction.'" method is not public method.');
		}

	}
	# set Shell

	public function setShell($argv){

		if(empty($argv[0])){
			$argv[0]="main";
		}

		$shellName=ucfirst($argv[0])."Shell";

		$shellPath=MK2_PATH_APP_SHELL.$shellName.".php";

		if(!file_exists($shellPath)){
			throw new \Exception('Not Found "'.$shellName.'" File.');
		}

		include($shellPath);

		$shellClassName=MK2_NAMESPACE."\\".$shellName;
		if(empty(class_exists($shellClassName))){
			throw new \Exception('Not Found "'.$shellClassName.'" Class.');
		}

		$shell=new $shellClassName();

		if(empty($argv[1])){
			$argv[1]="index";
		}

		$shellAction=$argv[1];
		$this->shellCheckModifier($shell,$shellAction,$shellClassName);

		$out=$shell->{$shellAction}();

		echo $out;
	}

}