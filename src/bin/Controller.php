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
				$Render=new Render(array(
					"__view_output"=>$this->__view_output,
				));

				# Set UI
				if(!empty($this->PackerUI)){
					$Render->UI=new \stdClass();
					foreach($this->PackerUI as $key=>$opt){
						$Render->UI->{$key}=$opt;
					}
				}

				$Render->rendering(@$this->Template,@$this->render);

			}
		}

		echo $out;

		//unset(memory suppression)
		unset($out);
		unset($View);

	}

	# (protected) getRender

	protected function getRender($renderName=null){

		//set Template
		if(!empty($this->__view_output)){
			foreach($this->__view_output as $key=>$o_){
				$$key=$o_;
			}
		}

		$view_url=MK2_PATH_APP_RENDER.ucfirst(Request::$params["controller"])."/";
		if(!empty($this->render)){
			$view_url.=$this->render.".view";
		}
		else
		{
			$view_url.=Request::$params["action"].".view";
		}
			
		ob_start();
		include($view_url);
		$contents=ob_get_contents();
		ob_end_clean();

		return $contents;
	}
}