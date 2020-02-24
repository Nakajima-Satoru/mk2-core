<?php

/*

mk2 | Controller

Basic class of Controller.
The 'Controller' changes the contents of processing and display based on the request from the user.
In the case of the basic MVC framework, Controller often has a core meaning.

Copylight(C) Nakajima Satoru 2020.

*/

namespace mk2\core;

class Controller extends CoreBlock{

	public $Template=false;
	public $autoRender=true;
	public $renderBase=null;
	public $actionPass=false;
	public $renderClass=null;

	# _settings

	public function __construct($option=null){
		parent::__construct($option);

		// default responseHeader setting
		$defHeader=Config::get("defHeader");
		if(!empty($defHeader)){
			foreach($defHeader as $k_=>$v_){
				@header($k_.": ".$v_);
			}
		}

	}

	# Rendering Method
	
	public function ___rendering($out){

		$use_class=Config::get("useClass");
		if(!empty($use_class["Render"])){
			if($this->autoRender){

				if(empty($this->renderClass)){
					$this->renderClass=Request::$params["controller"];
				}

				# set Render Class
				$Render=$this->_setRender($this->renderClass);

				if($Render){
					$Render->renderBase=$this->renderBase;
					$Render->rendering(@$this->Template,@$this->render);
				}

			}
		}

		echo $out;

		//unset(memory suppression)
		unset($out);
		unset($View);

	}

	# (protected) getRender

	protected function getRender($renderName=null,$renderClassName=null,$controllerName=null){

		if(!$renderName){
			if(!empty($this->render)){
				$renderName=$this->render;
			}
			else
			{
				$renderName=Request::$params["action"];
			}
		}
		if(!$renderClassName){
			$renderClassName=Request::$params["controller"];
		}
		if(!$controllerName){
			$controllerName=Request::$params["controller"];
		}
		
		return parent::getRender($renderName,$renderClassName,$controllerName);

	}

	# (protected) getRenderPath

	protected function getRenderPath(){
		
		$view_url=MK2_PATH_APP_RENDER.ucfirst(Request::$params["controller"])."/";
		if(!empty($this->render)){
			$view_url.=$this->render.MK2_RENDERING_EXTENSION;
		}
		else
		{
			$view_url.=Request::$params["action"].MK2_RENDERING_EXTENSION;
		}
		return $view_url;

	}

}