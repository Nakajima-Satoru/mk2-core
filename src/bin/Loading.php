<?php

namespace mk2\core;

class Loading{

	public function __construct(&$context){
		$this->context=$context;
	}

	# Model
	# By enumerating the Model class you want to use here, it will be loaded automatically.
	public function Model($params,$addAllow=null){
		// add class loading
		CoreBlockStatic::_addClassLoading($this->context,"Model",$params,$addAllow);
		return $this;
	}

	# Table
	# By enumerating the (DB)Table class you want to use here, it will be loaded automatically.
	public function Table($params,$addAllow=null){
		// add class loading
		CoreBlockStatic::_addClassLoading($this->context,"Table",$params,$addAllow);
		return $this;
	}

	# Validator
	# By enumerating the Validator class you want to use here, it will be loaded automatically.
	public function Validator($params,$addAllow=null){
		// add class loading
		CoreBlockStatic::_addClassLoading($this->context,"Validator",$params,$addAllow);
		return $this;
	}

	# Packer
	# By enumerating the Packer class you want to use here, it will be loaded automatically.
	public function Packer($params,$addAllow=null){
		// add class loading
		CoreBlockStatic::_addClassLoading($this->context,"Packer",$params,$addAllow);
		return $this;
	}

	# Controller
	# By enumerating the Controller class you want to use here, it will be loaded automatically.
	public function Controller($params,$addAllow=null){
		// add class loading
		CoreBlockStatic::_addClassLoading($this->context,"Controller",$params,$addAllow);
		return $this;
	}

	# Shell
	# By enumerating the Shell class you want to use here, it will be loaded automatically.
	public function Shell($params,$addAllow=null){
		// add class loading
		CoreBlockStatic::_addClassLoading($this->context,"Shell",$params,$addAllow);
		return $this;
	}

	# Render
	# By enumerating the Render class you want to use here, it will be loaded automatically.
	public function Render($params,$addAllow=null){
		CoreBlockStatic::_addClassLoading($this->context,"Render",$params,$addAllow);
		return $this;
	}

}