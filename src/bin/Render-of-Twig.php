<?php

/*

mk2 | Render-of-Twig

A class for outputting HTML tags on the screen etc. and displaying the layout.
Render class when using template engine "Twig".

Copylight(C) Nakajima Satoru 2020.

*/

namespace mk2\core;

class Render{

	public $__view_output=[];
	public $templateEngine=null;

	public function __construct($params=array()){
		
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

		if($this->Template){

			$template_url=$this->Template.MK2_RENDERING_EXTENSION;

			$twigLoader = new \Twig\Loader\FilesystemLoader(MK2_PATH_APP_TEMPLATE);
			$Twig = new \Twig\Environment($twigLoader,[
				'debug' => true,
			]);
			$Twig->addExtension(new \Twig\Extension\DebugExtension());

			$setData=$this->__view_output;
			$setData["this"]=$this;

			echo $Twig->render($template_url,$setData);

		}
		else
		{

			//$renderUrl=ucfirst(Request::$params["controller"])."/";

			if($controllerName){
				$renderUrl=ucfirst($controllerName)."/";
			}
			else
			{
				$renderUrl="";
			}

			if(!empty($this->render)){
				$renderUrl.=$this->render.MK2_RENDERING_EXTENSION;
			}
			else
			{
				$renderUrl.=Request::$params["action"].MK2_RENDERING_EXTENSION;
			}

			if(!empty(file_exists(MK2_PATH_APP_RENDER.$renderUrl))){

				$twigLoader = new \Twig\Loader\FilesystemLoader(MK2_PATH_APP_RENDER);
				$Twig = new \Twig\Environment($twigLoader,[
					'debug' => true,
				]);
				$Twig->addExtension(new \Twig\Extension\DebugExtension());

				$setData=$this->__view_output;
				$setData["this"]=$this;

				echo $Twig->render($renderUrl,$setData);
	
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

		}

		$renderUrl=ucfirst(Request::$params["controller"])."/";

		if(!empty($this->render)){
			$renderUrl.=$this->render.MK2_RENDERING_EXTENSION;
		}
		else
		{
			$renderUrl.=Request::$params["action"].MK2_RENDERING_EXTENSION;
		}

		$twigLoader = new \Twig\Loader\FilesystemLoader(MK2_PATH_APP_RENDER);
		$Twig = new \Twig\Environment($twigLoader,[
			'debug' => true,
		]);
		$Twig->addExtension(new \Twig\Extension\DebugExtension());

		$setData=$this->__view_output;
		$setData["this"]=$this;

		echo $Twig->render($renderUrl,$setData);

	}

	# getViewPart

	public function getViewPart($name,$oBuff=false){

		//set layout
		if(!empty($this->__view_output)){
			foreach($this->__view_output as $key=>$o_){
				$$key=$o_;
			}
		}

		$partUrl=$name.MK2_RENDERING_EXTENSION;

		$twigLoader = new \Twig\Loader\FilesystemLoader(MK2_PATH_APP_VIEWPART);
		$Twig = new \Twig\Environment($twigLoader,[
			'debug' => true,
		]);
		$Twig->addExtension(new \Twig\Extension\DebugExtension());

		$setData=$this->__view_output;
		$setData["this"]=$this;

		echo $Twig->render($partUrl,$setData);

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