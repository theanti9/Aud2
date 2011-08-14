<?php

class Settings {
	
	public $AppName;

	public function __construct($appname) {
		$this->AppName = $appname;
	}

	public function __get($var) {
		return $this->$var;
	}

	public function __set($var, $val) {
		$this->$var = $val;
	}

}


?>