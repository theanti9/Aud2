<?php
class StringBuilder {
	private $str = array();
	
	public function __construct() { }
	
	public function append($str) {
		$this->str[] = $str;
		return $this;
	}
	
	public function toString() {
		return implode($this->str);
	}
}


?>