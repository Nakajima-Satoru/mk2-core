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

			$template_url=MK2_PATH_APP_TEMPLATE.$this->Template.MK2_RENDERING_EXTENSION;
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
					$renderUrl=$this->renderbase.$this->render.MK2_RENDERING_EXTENSION;
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
					$renderUrl.=Request::$params["action"].MK2_RENDERING_EXTENSION;
				}
			}

			if(!empty(file_exists($renderUrl))){
				include($renderUrl);
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

	protected function getUrl($params){

		if(is_array($params)){

			$urla="";

			if(!empty($params["head"])){
				$urla.=$params["head"]."/";
				unset($params["head"]);
			}

			if(empty($params["controller"])){
				$params["controller"]=$this->request->params["controller"];
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

			return $this->request->params["root"].$urla;

		}
		else
		{
			if($params=="/"){
				return $this->request->params["root"];
			}
			else
			{
				if($params[0]=="@"){
					return $this->request->params["root"].substr($params,1);
				}
				else
				{
					return $params;
				}
			}
		}

	}

	# (protected) getRender

	protected function getRender($oBuff=false){

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

		include($renderUrl);

		if($oBuff){
			$contents=ob_get_contents();
			ob_end_clean();
			return $contents;
		}

	}

	# (protected) getViewPart
	
	protected function getViewPart($name,$oBuff=false){

		//set layout
		if(!empty($this->__view_output)){
			foreach($this->__view_output as $key=>$o_){
				$$key=$o_;
			}
		}

		if(!empty($this->renderBase)){
			$partUrl=$this->renderBase.$name.MK2_RENDERING_EXTENSION;
		}
		else{
			$partUrl=MK2_PATH_APP_VIEWPART.$name.MK2_RENDERING_EXTENSION;
		}

		if($oBuff){
			ob_start();
		}

		include($partUrl);

		if($oBuff){
			$contents=ob_get_contents();
			ob_end_clean();
			return $contents;
		}

	}

	# (protected) existViewPart

	protected function existViewPart($name){

		if(!empty($this->renderBase)){

			$path=$this->renderBase.$name.MK2_RENDERING_EXTENSION;

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