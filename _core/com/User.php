<?php

class User
{
	
	protected $_data;
	
	private $_db;
	private $_id_app;
	private $_code;
	
	private $_session;
	
	protected $loggedin;

	public function __construct($db, $id_app, &$session) {
		$this->_db = $db;
		$this->_id_app = $id_app;
		
		$this->_session = $session;
		
	}
	
	/*
	public function __get($var) {
		for($i=0; $i<$this->_count; $i++) {
			if($this->_data[$i]['code'] == $var){
				return $this->_data[$i]['value'];
			}
		}
		return '';
	}
	public function __set($var, $value) {
		$this->_data[$var] = $value;
	}
	
	*/
	
	/*
	public function __isset($var) {
		return isset($this->_data[$var]);
	}
	
	public function __unset($var) {
		unset($this->_data[$var]);
	}
	*/
	/*
	public function __call($method, $args) {
		echo 'De method User::'.$method.' is niet gedeclareerd';
	}
	
	
	public function __toString() {
		return $this->_code;
	} 
	
	*/
	
}

?>