<?php

/*

mk2 | Render-of-Smarty

A class for outputting HTML tags on the screen etc. and displaying the layout.
Render class when using template engine "Smarty".

Copylight(C) Nakajima Satoru 2020.

*/

namespace mk2\core;

class Render{

	public $__view_output=[];
	public $templateEngine=null;

	public function __construct($params=array()){

		$teCheck=false;
		if(class_exists("Smarty")){
			$this->Smarty=new \Smarty();
			$teCheck=true;
		}

		if(!$teCheck){
			throw new \Exception('template Engine not found "Smarty".');
		}

		if(!empty($params["__view_output"])){
			$this->__view_output=$params["__view_output"];
		}

		# request
		$this->request=Request::getall();

	}

	//rendering
	public function rendering($template=null,$render=null,$controllerName=null){

		$this->Template=$template;
		$this->render=$render;

		if(!empty($this->__view_output)){
			foreach($this->__view_output as $key=>$o_){
				$this->Smarty->assign($key,$o_);
			}
		}

		if($this->Template){

			$template_url=MK2_PATH_APP_TEMPLATE.$this->Template.MK2_RENDERING_EXTENSION;

			$this->Smarty->assign('this',$this);
			$this->Smarty->display($template_url);

		}
		else
		{

			if($controllerName){
				$renderUrl=MK2_PATH_APP_RENDER.ucfirst($controllerName)."/";
			}
			else
			{
				$renderUrl=MK2_PATH_APP_RENDER;
			}

			if(!empty($this->render)){
				$renderUrl.=$this->render.MK2_RENDERING_EXTENSION;
			}
			else
			{
				$renderUrl.=Request::$params["action"].MK2_RENDERING_EXTENSION;
			}

			if(!empty(file_exists($renderUrl))){

				$this->Smarty->assign('this',$this);
				$this->Smarty->display($renderUrl);
	
			}
			else
			{

				$errText= 'Render not Found : A Render file necessary for screen display does not exist.'."\n";
				$errText= 'Please check if the Render file exists in the following directory.\n'."\n";
				$errText.= 'Path : '.$renderUrl;
				echo $errText;

			}
		}

	}

	# (protected) set

	public function set($name,$value){
		$this->__view_output[$name]=$value;
	}

	# (protected) getUrl

	public function getUrl($params){
		return CoreBlockStatic::_getUrl($params);
	}

	# getRender

	public function getRender($oBuff=false){

		//set layout
		if(!empty($this->__view_output)){
			foreach($this->__view_output as $key=>$o_){
				$this->Smarty->assign($key,$o_);
			}
		}

		$renderUrl=MK2_PATH_APP_RENDER.ucfirst(Request::$params["controller"])."/";

		if(!empty($this->render)){
			$renderUrl.=$this->render.MK2_RENDERING_EXTENSION;
		}
		else
		{
			$renderUrl.=Request::$params["action"].MK2_RENDERING_EXTENSION;
		}

		$this->Smarty->assign("this",$this);
		$this->Smarty->display($renderUrl);

	}

	# getViewPart

	public function getViewPart($name,$oBuff=false){

		//set layout
		if(!empty($this->__view_output)){
			foreach($this->__view_output as $key=>$o_){
				$this->Smarty->assign($key,$o_);
			}
		}

		$partUrl=MK2_PATH_APP_VIEWPART.$name.MK2_RENDERING_EXTENSION;

		$this->Smarty->assign("this",$this);
		$this->Smarty->display($partUrl);

	}

	# existViewPart

	public function existViewPart($name){

		if(file_exists(MK2_PATH_APP_VIEWPART.$name.MK2_RENDERING_EXTENSION)){
			return true;
		}
		else
		{
			return false;
		}
	}
}