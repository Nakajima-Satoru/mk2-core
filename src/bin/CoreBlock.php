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
	public $render=null;
	public $view=null;
	public $Template=null;
	public $renderBase=null;
	public $renderBaseView=null;
	public $renderBaseTemplate=null;
	public $renderBaseViewPart=null;
	public $templateEngine=null;

	# protected $_obj=[];

	public function __construct($option=null){

		# option setting
		if(!empty($option)){
			foreach($option as $key=>$o_){
				$this->{$key}=$o_;
			}
		}

		$this->templateEngine=Config::get("templateEngine");

		if($this->templateEngine=="Smarty"){

			if(!class_exists("Smarty")){
				throw new \Exception('Template engine "Smarty" class not prepared.');
			}

			$this->Smarty=new \Smarty();
		}
		else if($this->templateEngine=="Twig"){

			if(!class_exists("Twig\Loader\FilesystemLoader")){
				throw new \Exception('Template engine "Twig" not prepared.');
			}

		}

	}

	# set model
	# By enumerating the Model class you want to use here, it will be loaded automatically.
	public function setModel($params,$addAllow=null){
		// add class loading
		CoreBlockStatic::_addClassLoading($this,"Model",$params,$addAllow);
		return $this;
	}

	# set Table
	# By enumerating the (DB)Table class you want to use here, it will be loaded automatically.
	public function setTable($params,$addAllow=null){
		// add class loading
		CoreBlockStatic::_addClassLoading($this,"Table",$params,$addAllow);
		return $this;
	}

	# set Validator
	# By enumerating the Validator class you want to use here, it will be loaded automatically.
	public function setValidator($params,$addAllow=null){
		// add class loading
		CoreBlockStatic::_addClassLoading($this,"Validator",$params,$addAllow);
		return $this;
	}

	# set packer
	# By enumerating the Packer class you want to use here, it will be loaded automatically.
	public function setPacker($params,$addAllow=null){
		// add class loading
		CoreBlockStatic::_addClassLoading($this,"Packer",$params,$addAllow);
		return $this;
	}

	# set controller
	# By enumerating the Controller class you want to use here, it will be loaded automatically.
	public function setController($params,$addAllow=null){
		// add class loading
		CoreBlockStatic::_addClassLoading($this,"Controller",$params,$addAllow);
		return $this;
	}

	# set Shell
	# By enumerating the Shell class you want to use here, it will be loaded automatically.
	public function setShell($params,$addAllow=null){
		// add class loading
		CoreBlockStatic::_addClassLoading($this,"Shell",$params,$addAllow);
		return $this;
	}
	# set Render
	# By enumerating the Render class you want to use here, it will be loaded automatically.
	public function setRender($params,$addAllow=null){
		CoreBlockStatic::_addClassLoading($this,"Render",$params,$addAllow);
		return $this;
	}

	# getUrl
	public function getUrl($params){
		return CoreBlockStatic::_getUrl($params);
	}

	# redirect
	public function redirect($params){
		$url=$this->getUrl($params);
		header("Location: ".$url);
		exit;
	}

	# View values set
	public function set($name,$value){
		$this->__view_output[$name]=$value;
	}

	// include
	public function _include($path,$outputBuffer=false){
		try{

			if(!empty($this->__view_output)){
				foreach($this->__view_output as $key=>$o_){
					$$key=$o_;
				}
			}

			if(!empty($this->templateEngine)){

				if($this->templateEngine=="Smarty"){
					$this->Smarty->assign('this',$this);
					
					if($outputBuffer){
						ob_start();
					}
		
					$this->Smarty->display($path);

					if($outputBuffer){
						$contents=ob_get_contents();
						ob_end_clean();
				
						return $contents;	
					}
				}
				else if($this->templateEngine=="Twig"){

					$twigLoader = new \Twig\Loader\FilesystemLoader(dirname($path));
					$Twig = new \Twig\Environment($twigLoader,[
						'debug' => true,
					]);
					$Twig->addExtension(new \Twig\Extension\DebugExtension());
		
					$setData=$this->__view_output;
					$setData["this"]=$this;
		
					echo $Twig->render(basename($path),$setData);

				}

			}
			else{

				if($outputBuffer){
					ob_start();
				}
	
				include($path);
	
				if($outputBuffer){
					$contents=ob_get_contents();
					ob_end_clean();
			
					return $contents;	
				}
	
			}

		}catch(\Exception $e){
			echo $e;
		}
	}

	// getTemplate
	public function getTemplate($templateName=null,$outputBuffer=false){

		$templateUrl=$this->getTemplatePath($templateName);

		if(!file_exists($templateUrl)){
			throw new \Exception('template file not found "'.$templateUrl.'"'."\n");
		}

		return $this->_include($templateUrl,$outputBuffer);
	
	}

	// getTemplatePath
	public function getTemplatePath($templateName=null){
		if(!$templateName){
			if($this->Template){
				$templateName=$this->Template;
			}
			else{
				return;
			}
		}

		$templateUrl=MK2_PATH_APP_TEMPLATE.$templateName.MK2_RENDERING_EXTENSION;
		if(!empty($this->renderBaseTemplate)){
			$templateUrl=$this->renderBaseTemplate.$templateName.MK2_RENDERING_EXTENSION;
		}
		return $templateUrl;
	}

	// existTemplate
	public function existTemplate($templateName=null){

		$path=$this->getTemplatePath($templateName);

		if(file_exists($path)){
			return true;
		}

	}

	// getView
	public function getView($viewName=null,$outputBuffer=false){

		$viewUrl=$this->getViewPath($viewName);
		
		if(!$viewUrl){
			return;
		}
		if(!file_exists($viewUrl)){
			throw new \Exception('view file not found "'.$viewUrl.'"'."\n");
		}

		return $this->_include($viewUrl,$outputBuffer);
		
	}

	// getViewPath
	public function getViewPath($viewName=null){

		if(!$viewName){
			if($this->view){
				$viewName=ucfirst(Request::$params["controller"])."/".$this->view;
			}
			else{
				return;
			}
		}

		$viewUrl=MK2_PATH_APP_VIEW.$viewName.MK2_RENDERING_EXTENSION;
		if(!empty($this->renderBaseView)){
			$viewUrl=$this->renderBaseView.$viewName.MK2_RENDERING_EXTENSION;
		}
		return $viewUrl;

	}

	// existView
	public function existView($viewName=null){

		$path=$this->getViewPath($viewName);

		if(!$path){
			return true;
		}

		if(file_exists($path)){
			return true;
		}

	}

	// getViewPart
	public function getViewPart($viewPartName,$outputBuffer=false){

		$partUrl=$this->getViewPartPath($viewPathName);

		if(!file_exists($partUrl)){
			throw new \Exception('ViewPart file not found "'.$partUrl.'"'."\n");
		}
	
		return $this->_include($partUrl,$outputBuffer);

	}

	// getViewPartPath
	public function getViewPartPath($viewPathName){
		if(!empty($this->renderBaseViewPart)){
			$partUrl=$this->renderBaseViewPart.$viewPartName.MK2_RENDERING_EXTENSION;
		}
		else
		{
			$partUrl=MK2_PATH_APP_VIEWPART.$viewPartName.MK2_RENDERING_EXTENSION;
		}

		return $partUrl;
	}

	// existViewPart
	public function existViewPart($partName){

		$path=$this->getViewPartPath($partName);
		
		if(file_exists($path)){
			return true;
		}
		
	}

	// existRender
	public function existRender($renderName=null){

		if($renderName){
			$path=MK2_PATH_APP_RENDER.ucfirst($renderName)."Render.php";
		}
		else{
			if($this->render){
				$path=MK2_PATH_APP_RENDER.ucfirst($this->render)."Render.php";
			}
			else{
				return true;
			}
		}

		if(file_exists($path)){
			return true;
		}

	}
	
}
class CoreBlock{
	use traitCoreBlock;
}