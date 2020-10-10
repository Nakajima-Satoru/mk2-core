<?php

/**
 * 
 * mk2 Render Class
 * 
 * A class for outputting HTML tags on the screen etc. and displaying the layout.
 * 
 * @copyright	 Copyright (C) Nakajima Satoru. 
 * @link		 https://www.mk2-php.com/
 *  
 */

namespace mk2\core;

class Render extends CoreBlock{

	public function rendering(){

		$this->request=Request::getAll();

		if($this->Template){
			return $this->Response->getTemplate();
		}
		else{
			return $this->Response->getView();
		}
	}
}