<?php
/*

mk2 | CoreBlock

CoreBlock is the base parent class for all class elements.
It will not be used by itself, it will change to a class according to the purpose such as Controller or Model.

Copylight(C) Nakajima Satoru 2020.

*/

namespace mk2\core;

include_once("CoreBlockStatic.php");
include_once("Loading.php");
include_once("Response.php");

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
		
		$this->Loading=new Loading($this);
		$this->Response=new Response($this);

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
	
}
class CoreBlock{
	use traitCoreBlock;
}