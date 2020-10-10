<?php

/**
 * 
 * mk2 Command Class
 * 
 * @copyright	 Copyright (C) Nakajima Satoru. 
 * @link		 https://www.mk2-php.com/
 * 
 */

namespace mk2\core;

class Command{

	public function text($output,$inline=false){
		echo $this->_out($output,null,$inline);
		return $this;
	}
	public function blue($output,$inline=false){
		echo $this->_out($output,'0;34',$inline);
		return $this;
	}
	public function green($output,$inline=false){
		echo $this->_out($output,'0;32',$inline);
		return $this;
	}
	public function cyan($output,$inline=false){
		echo $this->_out($output,'0;36',$inline);
		return $this;
	}
	public function red($output,$inline=false){
		echo $this->_out($output,'0;31',$inline);
		return $this;
	}
	public function purple($output,$inline=false){
		echo $this->_out($output,'0;35',$inline);
		return $this;
	}
	public function brown($output,$inline=false){
		echo $this->_out($output,'0;33',$inline);
		return $this;
	}
	public function lightGray($output,$inline=false){
		echo $this->_out($output,'0;37',$inline);
		return $this;
	}
	public function darkGray($output,$inline=false){
		echo $this->_out($output,'1;30',$inline);
		return $this;
	}
	public function lightBlue($output,$inline=false){
		echo $this->_out($output,'1;34',$inline);
		return $this;
	}
	public function lightGreen($output,$inline=false){
		echo $this->_out($output,'1;32',$inline);
		return $this;
	}
	public function lightCyan($output,$inline=false){
		echo $this->_out($output,'1;36',$inline);
		return $this;
	}
	public function lightRed($output,$inline=false){
		echo $this->_out($output,'1;31',$inline);
		return $this;
	}
	public function lightPurple($output,$inline=false){
		echo $this->_out($output,'1;35',$inline);
		return $this;
	}
	public function yellow($output,$inline=false){
		echo $this->_out($output,'1;33',$inline);
		return $this;
	}
	public function white($output,$inline=false){
		echo $this->_out($output,'1;37',$inline);
		return $this;
	}
	private function _out($output,$color=null,$inline){

		$str=$output;
		if($color){
			$str="\033[".$color."m".$output."\033[0m";
		}

		if(!$inline){
			$str.="\n";
		}
		return $str;
	}

	public function input($output){
		echo $output." : ";
		return trim(fgets(STDIN));
	}

/*

■背景色

Black	44
Cyan	46
Red	41
Purple	45
Brown	43
Light Gray	47


*/
}