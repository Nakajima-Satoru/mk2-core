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

		$use_class=Config::get("useClass");

		# request
		$this->request=Request::getall();

	}

	//rendering
	public function rendering($template,$render){

		$this->Template=$template;
		$this->render=$render;

		if(!empty($this->__view_output)){
			foreach($this->__view_output as $key=>$o_){
				$this->Smarty->assign($key,$o_);
			}
		}

		if($this->Template){

			$template_url=MK2_PATH_APP_TEMPLATE.$this->Template.".view";

			$this->Smarty->assign('this',$this);
			$this->Smarty->display($template_url);

		}
		else
		{

			$renderUrl=MK2_PATH_APP_RENDER.ucfirst(Request::$params["controller"])."/";

			if(!empty($this->render)){
				$renderUrl.=$this->render.".view";
			}
			else
			{
				$renderUrl.=Request::$params["action"].".view";
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
			$renderUrl.=$this->render.".view";
		}
		else
		{
			$renderUrl.=Request::$params["action"].".view";
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

		$partUrl=MK2_PATH_APP_VIEWPART.$name.".view";

		$this->Smarty->assign("this",$this);
		$this->Smarty->display($partUrl);

	}

	# existViewPart

	public function existViewPart($name){

		if(file_exists(MK2_PATH_APP_VIEWPART.$name.".view")){
			return true;
		}
		else
		{
			return false;
		}
	}
}