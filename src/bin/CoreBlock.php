<?php

/**
 * 
 * mk2 CoreBlock Class
 * 
 * CoreBlock is the base parent class for all class elements.
 * It will not be used by itself, it will change to a class according to the purpose such as Controller or Model.
 * 
 * @copyright	 Copyright (C) Nakajima Satoru. 
 * @link		 https://www.mk2-php.com/
 *  
 */

namespace mk2\core;

include_once("CoreBlockStatic.php");
include_once("Loading.php");
include_once("Response.php");

/**
 *  Correspond with trait
 */ 
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

	public function __construct($option=null){

		// option setting
		if(!empty($option)){
			foreach($option as $key=>$o_){
				$this->{$key}=$o_;
			}
		}

		// Set Loading Class
		$this->Loading=new Loading($this);

		// Set Response Class
		$this->Response=new Response($this);
		if(php_sapi_name()=="cli"){

			// Set Command Class (CLI Mode Only)
			include_once("Command.php");
			$this->Command=new Command($this);

		}

		$this->templateEngine=Config::get("templateEngine");

		if($this->templateEngine=="Smarty"){

			// Set Template Engine (Smarty)
			if(!class_exists("Smarty")){
				throw new \Exception('Template engine "Smarty" class not prepared.');
			}

			$this->Smarty=new \Smarty();
			$this->Smarty->compile_dir=MK2_PATH_APP_TEMPORARY."smarty";
		}
		else if($this->templateEngine=="Twig"){

			// Set Template Engine (Twig)
			if(!class_exists("Twig\Loader\FilesystemLoader")){
				throw new \Exception('Template engine "Twig" not prepared.');
			}

		}

	}

	/**
	 * include
	 */
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
		}catch(\Error $e){
			echo $e;
		}
	}
	
}
class CoreBlock{
	use traitCoreBlock;
}