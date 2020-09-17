<?php

/**
 * Mark2 | Mk2CLI
 * 
 * Photoshop library for mk2 command execution.
 * 
 * Copylight(C) Nakajima Satoru 2020.
 * URL https://www.mk2-php.com/
 * 
 */

namespace mk2\core;

class Mk2CLI{

	private const CONFIG_DATABASE="database";

	/**
	 * constructor
	 */

	public function __construct(){

		try{

			// Loading core Libraries.
			$this->loadingLibs();
			
			// Loading Application Config Data.
			$this->loadingApps();
	
			// Loading Library Customise.
			$this->loadingLibsCustom();

			$argv=$_SERVER["argv"];
			array_shift($argv);

			$this->setShell($argv);

		}catch(\Exception $e){
			print_r($e);
		}
		catch(\Error $e){
			print_r($e);
		}
		
	}

	# (private) loading Libraries

	private function loadingLibs(){

		# Include basic core libraries.
		include("bin/Construct.php");
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
		if(!empty(Config::get("include"))){
			$includes=Config::get("include");
			foreach($includes as $o_){
				if(file_exists(MK2_PATH_APP.$o_)){
					include(MK2_PATH_APP.$o_);
				}
			}
		}

		if(is_string(Config::get(self::CONFIG_DATABASE))){
			$getDatabase=include(Config::get(self::CONFIG_DATABASE));
			Config::setDetail(self::CONFIG_DATABASE,$getDatabase);
		}

	}

	# (private) loading Libraries custom

	private function loadingLibsCustom(){

		# get Use Class
		$class=Config::get("class");

		if(!$class){
			return;
		}

		foreach($class as $className=>$u_){
			if($className!="Render"){
				include_once("bin/".$className.".php");
			}
		}

	}

	# (private) shellCheckModifier

	private function shellCheckModifier($shell,$shellAction,$shellClassName){

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

		# filter before hook.
		if(method_exists($shell,"filterBefore")){
			$shell->filterBefore();
		}

		array_shift($argv);

		$request=[];
		$shellAction="index";
		foreach($argv as $ind=>$a_){
			if($a_[0]=="-"){
				$as_=explode("=",substr($a_,1));
				if(count($as_)==1){
					$request[$as_[0]]=true;
				}
				else{
					$request[$as_[0]]=$as_[1];
				}
			}
			else{
				if($ind==0){
					$shellAction=$a_;
				}
			}
		}

		$this->shellCheckModifier($shell,$shellAction,$shellClassName);

		if($request){
			$out=$shell->{$shellAction}($request);
		}
		else{
			$out=$shell->{$shellAction}();
		}

		echo $out;

	}

}