<?php
/*

mk2 | CoreBlock

CoreBlock is the base parent class for all class elements.
It will not be used by itself, it will change to a class according to the purpose such as Controller or Model.

Copylight(C) Nakajima Satoru 2020.

*/

namespace mk2\core;

include_once("CoreBlockStatic.php");

# Correspond with trait

trait traitCoreBlock{

	public $__view_output=[];
	public $renderBase=null;
	public $renderBaseViewPart=null;

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

	protected function setting($params,$addAllow=null){

		foreach($params as $key=>$p_){
			if($key=="Model"){
				$this->setModel($p_,$addAllow);
			}
			else if($key=="Table"){
				$this->setTable($p_,$addAllow);
			}
			else if($key=="Validator"){
				$this->setValidator($p_,$addAllow);
			}
			else if($key=="Packer"){
				$this->setPacker($p_,$addAllow);
			}
			else if($key=="Controller"){
				$this->setController($p_,$addAllow);
			}
			else if($key=="Shell"){
				$this->setShell($p_,$addAllow);
			}
		}
		return $this;
	}

	# (protected) set model
	# By enumerating the Model class you want to use here, it will be loaded automatically.

	protected function setModel($params,$addAllow=null){

		// add class loading
		$this->_addClassLoading("Model",$params,$addAllow);
		return $this;

	}

	# (protected) set Table
	# By enumerating the (DB)Table class you want to use here, it will be loaded automatically.

	protected function setTable($params,$addAllow=null){

		// add class loading
		$this->_addClassLoading("Table",$params,$addAllow);
		return $this;

	}

	# (protected) set Validator
	# By enumerating the Validator class you want to use here, it will be loaded automatically.

	protected function setValidator($params,$addAllow=null){

		// add class loading
		$this->_addClassLoading("Validator",$params,$addAllow);
		return $this;

	}

	# (protected) set packer
	# By enumerating the Packer class you want to use here, it will be loaded automatically.

	protected function setPacker($params,$addAllow=null){

		// add class loading
		$this->_addClassLoading("Packer",$params,$addAllow);
		return $this;

	}

	# (protected) set controller
	# By enumerating the Controller class you want to use here, it will be loaded automatically.

	protected function setController($params,$addAllow=null){

		// add class loading
		$this->_addClassLoading("Controller",$params,$addAllow);
		return $this;
	
	}

	# (protected) set Shell
	# By enumerating the Shell class you want to use here, it will be loaded automatically.

	protected function setShell($params,$addAllow=null){

		// add class loading
		$this->_addClassLoading("Shell",$params,$addAllow);
	}

	# (private) _addClassLoading

	private function _addClassLoading($classType,$params,$addAllow=null){

		$out=CoreBlockStatic::_addClassLoading($classType,$params,$addAllow);

		foreach($out->{$classType} as $className=>$o_){
			if(empty($this->{$classType})){
				$this->{$classType}=new \stdClass();
			}

			$this->{$classType}->{$className}=$o_;
		}

		if($classType=="Packer"){
			foreach($out->PackerUI as $className=>$o_){
				if(empty($this->PackerUI)){
					$this->PackerUI=new \stdClass();
				}
	
				$this->PackerUI->{$className}=$o_;
			}
		}

	}

	# (protected) getUrl

	protected function getUrl($params){

		return CoreBlockStatic::_getUrl($params);

	}

	# (protected) redirect

	protected function redirect($params){
		
		$url=$this->getUrl($params);
		header("Location: ".$url);
		exit;
		
	}

	# (protected) gotoError

	protected function gotoError(){

		http_response_code(404);
		throw new \Exception("not found page.");
	}

	# (protected) View values set

	protected function set($name,$value){
		$this->__view_output[$name]=$value;
	}

	# (protected) getRender

	protected function getRender($renderName=null,$renderClassName=null,$controllerName=null){

		# Render Class Exist Check..
		if(class_exists("mk2\\core\\Render")){

			# set Render Class
			$Render=$this->_setRender($renderClassName);
			$Render->renderBase=$this->renderBase;
			$Render->renderBaseViewPart=$this->renderBaseViewPart;

			ob_start();
			$Render->rendering(null,$renderName,$controllerName);
			$contents=ob_get_contents();
			ob_end_clean();

			return $contents;

		}
		else
		{

			//set Template
			if(!empty($this->__view_output)){
				foreach($this->__view_output as $key=>$o_){
					$$key=$o_;
				}
			}

			if(!empty($this->renderBase)){
				$view_url=$this->renderBase.$renderName.MK2_RENDERING_EXTENSION;
			}
			else
			{
				$view_url=MK2_PATH_APP_RENDER.$renderName.MK2_RENDERING_EXTENSION;
			}

			ob_start();
			include($view_url);
			$contents=ob_get_contents();
			ob_end_clean();

			return $contents;

		}

	}

	# (protected) getPart

	protected function getViewPart($name){

		//set Template
		if(!empty($this->__view_output)){
			foreach($this->__view_output as $key=>$o_){
				$$key=$o_;
			}
		}

		if(!empty($this->renderBaseViewPart)){
			$part_url=$this->renderBaseViewPart.$name.MK2_RENDERING_EXTENSION;
		}
		else
		{
			$part_url=MK2_PATH_APP_VIEWPART.$name.MK2_RENDERING_EXTENSION;
		}


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

		if(!empty($this->renderBaseViewPart)){
			$path=$this->renderBaseViewPart.$name.MK2_RENDERING_EXTENSION;
		}
		else{
			$path=MK2_PATH_APP_VIEWPART.$name.MK2_RENDERING_EXTENSION;
		}

		if(file_exists($path)){
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

	# (protected) _setRender

	protected function _setRender($renderClassName=null){

		$renderUrl=null;
		if($renderClassName){
			$className=ucfirst($renderClassName);
			$renderUrl=MK2_PATH_APP_RENDER.ucfirst($className)."Render.php";
		}

		if(file_exists($renderUrl)){

			include($renderUrl);

			$renderClassName=MK2_NAMESPACE."\\".ucfirst($className)."Render";
	
			if(!class_exists($renderClassName)){
				$renderClassName="mk2\core\\".ucfirst($className)."Render";
			}
			if(!class_exists($renderClassName)){
				$renderClassName=ucfirst($className)."Render";
			}

			$Render=new $renderClassName(array(
				"__view_output"=>$this->__view_output,
			));
		}
		else
		{
			$Render=new Render(array(
				"__view_output"=>$this->__view_output,
			));	
		}
	
		# Set UI
		if(!empty($this->PackerUI)){
			$Render->UI=new \stdClass();
			foreach($this->PackerUI as $key=>$opt){
				$Render->UI->{$key}=$opt;
			}
		}
	
		return $Render;
			
	}
}
class CoreBlock{
	use traitCoreBlock;
}