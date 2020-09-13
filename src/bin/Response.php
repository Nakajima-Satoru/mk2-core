<?php

namespace mk2\core;

class Response{

	public function __construct(&$context){
		$this->context=$context;
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
		$this->context->__view_output[$name]=$value;
	}

	// getTemplate
	public function getTemplate($templateName=null,$outputBuffer=false){

		$templateUrl=$this->getTemplatePath($templateName);

		if(!file_exists($templateUrl)){
			throw new \Exception('template file not found "'.$templateUrl.'"'."\n");
		}

		return $this->context->_include($templateUrl,$outputBuffer);
	
	}

	// getTemplatePath
	public function getTemplatePath($templateName=null){
		if(!$templateName){
			if($this->context->Template){
				$templateName=$this->context->Template;
			}
			else{
				return;
			}
		}

		$templateUrl=MK2_PATH_APP_TEMPLATE.$templateName.MK2_RENDERING_EXTENSION;
		if(!empty($this->context->renderBaseTemplate)){
			$templateUrl=$this->context->renderBaseTemplate.$templateName.MK2_RENDERING_EXTENSION;
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

		return $this->context->_include($viewUrl,$outputBuffer);
		
	}

	// getViewPath
	public function getViewPath($viewName=null){

		if(!$viewName){
			if($this->context->view){
				$viewName=ucfirst(Request::$params["controller"])."/".$this->context->view;
			}
			else{
				return;
			}
		}

		$viewUrl=MK2_PATH_APP_VIEW.$viewName.MK2_RENDERING_EXTENSION;
		if(!empty($this->context->renderBaseView)){
			$viewUrl=$this->context->renderBaseView.$viewName.MK2_RENDERING_EXTENSION;
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
	
		return $this->context->_include($partUrl,$outputBuffer);

	}

	// getViewPartPath
	public function getViewPartPath($viewPathName){
		if(!empty($this->context->renderBaseViewPart)){
			$partUrl=$this->context->renderBaseViewPart.$viewPartName.MK2_RENDERING_EXTENSION;
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
			if($this->context->render){
				$path=MK2_PATH_APP_RENDER.ucfirst($this->context->render)."Render.php";
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