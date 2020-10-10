<?php

/**
 * 
 * mk2 Controller Class
 * 
 * Basic class of Controller.
 * The 'Controller' changes the contents of processing and display based on the request from the user.
 * In the case of the basic MVC framework, Controller often has a core meaning.
 * 
 * @copyright	 Copyright (C) Nakajima Satoru. 
 * @link		 https://www.mk2-php.com/
 * 
 */

 namespace mk2\core;

class Controller extends CoreBlock{

	public $autoRender=true;
	public $actionPass=false;

	// _settings
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

	// Rendering Method
	public function ___rendering($out){

		$class=Config::get("class");

		if(!empty($class["Render"]["enable"])){
			if($this->autoRender){

				$render="Render";
				if(!$this->render){
					$render=$this->render;
				}

				if(!$this->Response->existRender()){
					throw new \Exception('"'.ucfirst($this->render).'Render" Class File Not Found.');
				}

				$this->Loading->Render([
					$render=>[
						"templateEngine"=>$this->templateEngine,
						"__view_output"=>$this->__view_output,
						"Template"=>$this->Template,
						"view"=>$this->view,
						"renderBaseTemplate"=>$this->renderBaseTemplate,
						"renderBaseView"=>$this->renderBaseView,
						"renderBaseViewPart"=>$this->renderBaseViewPart,
					],
				]);

				if(empty($this->Render->{$render})){
					throw new \Exception('"'.ucfirst($this->render).'Render" Class Not found.');
				}

				$renderClass=$this->Render->{$render};

				unset($this->Render->{$render});

				if(!empty($this->UI)){
					$renderClass->UI=$this->UI;
				}

				$renderClass->rendering();

			}
		}

		echo $out;

	}

}