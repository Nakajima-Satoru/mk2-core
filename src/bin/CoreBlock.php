<?php
/*

mk2 | CoreBlock

CoreBlock is the base parent class for all class elements.
It will not be used by itself, it will change to a class according to the purpose such as Controller or Model.

Copylight(C) Nakajima Satoru 2020.

*/

namespace mk2\core;

# Correspond with trait

trait traitCoreBlock{

	public $__view_output=[];

	# protected $_obj=[];

	public function __construct($option=null){

		# option setting
		if(!empty($option)){
			foreach($option as $key=>$o_){
				$this->{$key}=$o_;
			}
		}
	}

	# (protected) setting
	
	protected function setting($params){

		foreach($params as $key=>$p_){
			if($key=="Model"){
				$this->setModel($p_);
			}
			else if($key=="Table"){
				$this->setTable($p_);
			}
			else if($key=="Validator"){
				$this->setValidator($p_);
			}
			else if($key=="Packer"){
				$this->setPacker($p_);
			}
			else if($key=="Controller"){
				$this->setController($p_);
			}
			else if($key=="Shell"){
				$this->setShell($p_);
			}
		}
		return $this;
	}

	# (protected) set model
	# By enumerating the Model class you want to use here, it will be loaded automatically.

	protected function setModel($params){

		// add class loading
		$this->_addClassLoading("Model",$params);
		return $this;

	}

	# (protected) set Table
	# By enumerating the (DB)Table class you want to use here, it will be loaded automatically.

	protected function setTable($params){

		// add class loading
		$this->_addClassLoading("Table",$params);
		return $this;

	}

	# (protected) set Validator
	# By enumerating the Validator class you want to use here, it will be loaded automatically.

	protected function setValidator($params){

		// add class loading
		$this->_addClassLoading("Validator",$params);
		return $this;

	}

	# (protected) set packer
	# By enumerating the Packer class you want to use here, it will be loaded automatically.

	protected function setPacker($params){

		// add class loading
		$this->_addClassLoading("Packer",$params,[MK2_PATH_VENDOR."mk2/packer/src/"]);
		return $this;

	}

	# (protected) set controller
	# By enumerating the Controller class you want to use here, it will be loaded automatically.

	protected function setController($params){

		// add class loading
		$this->_addClassLoading("Controller",$params);
		return $this;
	
	}

	# (protected) set Shell
	# By enumerating the Shell class you want to use here, it will be loaded automatically.

	protected function setShell($params){

		// add class loading
		$this->_addClassLoading("Shell",$params);
	}

	# (private) _addClassLoading

	private function _addClassLoading($classType,$params,$addAllow=null){

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
				$className=ucfirst($p_);
			}
			else
			{
				$className=ucfirst($key);
				$option=$p_;
			}

			# If Initialize file existe. Initialize data on marge.
			if(empty($option["_independent"])){
				$initPath=MK2_PATH_APPCONFINIT.$className.$classType."Init.php";
				if(file_exists($initPath)){
					$init=include_once($initPath);
					$option=array_merge($option,$init);
				}
			}
			else{
				unset($option["_independent"]);
			}

			$jugement=false;
			foreach($allowPathList as $m_){

				$url=$m_.$className.$classType.".php";

				if(!empty(file_exists($url))){
					$jugement=true;
					break;
				}
			}

			if($jugement){
				include_once($url);

				# namespace check

				$path=MK2_NAMESPACE."\\".$className.$classType;

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

					if(empty($this->{$classType})){
						$this->{$classType}=new \stdClass();
					}

					$this->{$classType}->{$className}=new $path($option);

					if($classType=="Table"){
						$this->Table->{$className}->_settingsModel();
					}
				}

				if($classType=="Packer"){

					if(class_exists($pathUseView)){

						if(empty($this->PackerUI)){
							$this->PackerUI=new \stdClass();
						}
						$classNameUseView=substr($classNameUseView,0,-2);
						$this->PackerUI->{$classNameUseView}=new $pathUseView($option);

					}
				}
			}
		}
	}

	# (protected) getUrl

	protected function getUrl($params){

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

	# (protected) redirect

	protected function redirect($params){
		
		$url=$this->getUrl($params);
		header("Location: ".$url);

	}

	# (protected) View values set

	protected function set($name,$value){
		$this->__view_output[$name]=$value;
	}

	# (protected) getRender

	protected function getRender($renderName){

		//set Template
		if(!empty($this->__view_output)){
			foreach($this->__view_output as $key=>$o_){
				$$key=$o_;
			}
		}

		$view_url=MK2_PATH_APP_RENDER.$renderName.MK2_RENDERING_EXTENSION;
			
		ob_start();
		include($view_url);
		$contents=ob_get_contents();
		ob_end_clean();

		return $contents;

	}

	# (protected) getPart

	protected function getViewPart($name){

		//set Template
		if(!empty($this->__view_output)){
			foreach($this->__view_output as $key=>$o_){
				$$key=$o_;
			}
		}

		$part_url=MK2_PATH_APP_VIEWPART.$name.MK2_RENDERING_EXTENSION;

		ob_start();
		include($part_url);
		$contents=ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	# (protected) existRender

	protected function existRender($name=null){

		$path=MK2_PATH_APP_RENDER.ucfirst(Request::$params["controller"])."/";
		if($name){
			$path.=$name.MK2_RENDERING_EXTENSION;
		}
		else
		{
			$path.=$this->render.MK2_RENDERING_EXTENSION;
		}

		if(file_exists($path)){
			return true;
		}
		else
		{
			return false;
		}

	}

	# (protected) existViewPart

	protected function existViewPart($name){

		if(file_exists(MK2_PATH_APP_VIEWPART.$name.MK2_RENDERING_EXTENSION)){
			return true;
		}
		else
		{
			return false;
		}

	}

	# (protected) existTemplate

	protected function existTemplate($name=null){

		$path=MK2_PATH_APP_TEMPLATE;
		if($name){
			$path.=$name.MK2_RENDERING_EXTENSION;
		}
		else
		{
			$path.=$this->layout.MK2_RENDERING_EXTENSION;
		}

		if(file_exists($path)){
			return true;
		}
		else
		{
			return false;
		}

	}
}
class CoreBlock{
	use traitCoreBlock;
}