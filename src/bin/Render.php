<?php

/*

mk2 | Render

A class for outputting HTML tags on the screen etc. and displaying the layout.

Copylight(C) Nakajima Satoru 2020.

*/

namespace mk2\core;

class Render{

	public $__view_output=[];
	public $renderBase=null;
	public $renderBaseViewPart=null;
	public $renderBaseTemplate=null;

	public function __construct($params=array()){

		if(!empty($params["__view_output"])){
			$this->__view_output=$params["__view_output"];
		}

		$use_class=Config::get("useClass");

		# request
		$this->request=Request::getall();

	}

	//rendering
	public function rendering($template=null,$render=null,$controllerName=null){

		$this->Template=$template;
		$this->render=$render;

		if(!empty($this->__view_output)){
			foreach($this->__view_output as $key=>$o_){
				$$key=$o_;
			}
		}

		if($this->Template){

			if(!empty($this->renderBaseTemplate)){
				$template_url=$this->renderBaseTemplate.$this->Template.MK2_RENDERING_EXTENSION;
			}
			else{
				$template_url=MK2_PATH_APP_TEMPLATE.$this->Template.MK2_RENDERING_EXTENSION;
			}

			include($template_url);

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
				if(!empty($this->renderBase)){
					$renderUrl=$this->renderBase.$this->render.MK2_RENDERING_EXTENSION;
				}
				else{
					$renderUrl.=$this->render.MK2_RENDERING_EXTENSION;
				}
			}
			else
			{
				if(!empty($this->renderBase)){
					$renderUrl=$this->renderBase.Request::$params["action"].MK2_RENDERING_EXTENSION;
				}
				else{
					$renderUrl.=ucfirst(Request::$params["controller"])."/".Request::$params["action"].MK2_RENDERING_EXTENSION;
				}
			}

			if(!empty(file_exists($renderUrl))){
				include($renderUrl);
			}
			else
			{
				if(!Config::get("debugMode")){
					echo "<pre>";
					$errText= 'Render not Found : A Render file necessary for screen display does not exist.'."\n";
					$errText= 'Please check if the Render file exists in the following directory.\n'."\n";
					$errText.= 'Path : '.$renderUrl;
					echo $errText;
					$e=new \Exception;
					echo $e;
					echo "</pre>";	
				}
			}
		}

	}

	# set

	public function set($name,$value){
		$this->__view_output[$name]=$value;
	}

	# getUrl

	public function getUrl($params){

		return CoreBlockStatic::_getUrl($params);
		
	}

	# getRender

	public function getRender($oBuff=false){

		try{
			//set layout
			if(!empty($this->__view_output)){
				foreach($this->__view_output as $key=>$o_){
					$$key=$o_;
				}
			}
			
			$renderUrl=MK2_PATH_APP_RENDER.ucfirst(Request::$params["controller"])."/";
			if(!empty($this->render)){
				if(!empty($this->renderBase)){
					$renderUrl=$this->renderBase.$this->render.MK2_RENDERING_EXTENSION;
				}
				else
				{
					$renderUrl.=$this->render.MK2_RENDERING_EXTENSION;
				}
			}
			else
			{
				if(!empty($this->renderBase)){
					$renderUrl=$this->renderBase.Request::$params["action"].MK2_RENDERING_EXTENSION;
				}
				else{
					$renderUrl.=Request::$params["action"].MK2_RENDERING_EXTENSION;
				}
			}

			if($oBuff){
				ob_start();
			}

			if(!file_exists($renderUrl)){
				throw new \Exception('render file not found "'.$renderUrl.'"'."\n");
			}
			include($renderUrl);

			if($oBuff){
				$contents=ob_get_contents();
				ob_end_clean();
				return $contents;
			}

		}catch(\Exception $e){
			if(!Config::get("debugMode")){
				echo '<pre style="text-align:left">';
				echo $e;
				echo '</pre>';
			}
		}

	}

	# getViewPart
	
	public function getViewPart($name,$oBuff=false){

		try{

			//set layout
			if(!empty($this->__view_output)){
				foreach($this->__view_output as $key=>$o_){
					$$key=$o_;
				}
			}

			if(!empty($this->renderBaseViewPart)){
				$partUrl=$this->renderBaseViewPart.$name.MK2_RENDERING_EXTENSION;
			}
			else{
				$partUrl=MK2_PATH_APP_VIEWPART.$name.MK2_RENDERING_EXTENSION;
			}

			if($oBuff){
				ob_start();
			}

			if(!file_exists($partUrl)){
				throw new \Exception('viewpart file not found "'.$partUrl.'"'."\n");
			}

			include($partUrl);

			if($oBuff){
				$contents=ob_get_contents();
				ob_end_clean();
				return $contents;
			}

		}catch(\Exception $e){
			if(!Config::get("debugMode")){
				echo "<pre style='text-align:left'>";
				echo $e;
				echo "</pre>";	
			}
		}

	}

	# existViewPart

	public function existViewPart($name){

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

}