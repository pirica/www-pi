<?php

class Action
{
	
	protected $_data;
	
	private $_db;
	private $_id_app;
	private $_code;
	private $_id_profile;
	
	private $_id_app_action;
	private $_page_title;
	private $_login_required;
	private $_allowed;
	
	public $default_code;

	public function __construct($db, $id_app, $code, $id_profile) {
		$this->_db = $db;
		$this->_id_app = $id_app;
		$this->_code = $code;
		$this->_id_profile = $id_profile;
		
		$this->_id_app_action = -1;
		$this->_page_title = '';
		$this->_login_required = 1;
		$this->_allowed = 0;
		
        $this->default_code = 'main';
        
		$this->getData();
		
		// action not found
		if($this->_id_app_action == -1){
			$this->setData();
			$this->getData();
		}
		
		// action still not found or could not add
		if($this->_id_app_action == -1){
			trigger_error('app action ' . $id_app . ':"' . $code . '" not defined!', E_USER_ERROR);
		}
		
	}
	
	public function getCode() {
		return $this->_code;
	}
	
	public function getId() {
		return $this->_id_app_action;
	}
	public function getPageTitle() {
		return $this->_page_title;
	}
	public function getLoginRequired() {
		return $this->_login_required;
	}
	public function getAllowed() {
		return $this->_allowed;
	}

	private function getData() {
		//if(count($this->_data) == 0){
			$this->_data = [];
			
			$this->_id_app_action = -1;
			//$this->_id_app,
			//$code,
			$this->_page_title = '';
			$this->_login_required = 1;
			$this->_allowed = 0;
			
			$code = ($this->_code == '' ? $this->default_code : $this->_code);
			
			$qry_action = mysql_query("
				select
					#aa.id_app,
					#aa.code,
					
					aa.id_app_action,
					aa.page_title,
					aa.login_required,
					
					case
						when p.full_access = 1 then 1
						when pa.allowed = 1 then 1
						when paa.allowed = 1 then 1
						else 0
					end as allowed
					
				from t_app_action aa
					join t_profile p on p.id_profile = " . $this->_id_profile . "
					left join t_profile_app pa on pa.id_app = aa.id_app and pa.id_profile = p.id_profile
					left join t_profile_app_action paa on paa.id_app_action = aa.id_app_action and paa.id_profile = p.id_profile
					
				where
					ifnull(aa.id_app, " . $this->_id_app . ") = " . $this->_id_app . "
					and aa.code = '" . mysql_real_escape_string($code) . "'
					and aa.active = 1
					
				order by
					aa.id_app desc
					
				limit 1
				
				", $this->_db);
				
            while($_action = mysql_fetch_array($qry_action)){
				$this->_id_app_action = $_action['id_app_action'];
				$this->_page_title = $_action['page_title'];
				$this->_login_required = $_action['login_required'];
				$this->_allowed = $_action['allowed'];
			}
			
		//}*/
		return $this->_data;
	}
	
	private function setData() {
        $code = ($this->_code == '' ? $this->default_code : $this->_code);
		
		mysql_query("
			insert into t_app_action
			(
				id_app,
				code
			)
			select
				nullif(" . $this->_id_app . ", -1),
				'" . mysql_real_escape_string($code) . "'
			from t_app_action
			where
				not exists (
					select * from t_app_action where ifnull(id_app,-1) = " . $this->_id_app . " and code = '" . mysql_real_escape_string($code) . "'
				)
			limit 1, 1
			
			", $this->_db);
			
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
	
	*/
	
	
	public function __toString() {
		return $this->_code;
	} 
	
	public function _debug() {
		print_r($this);
	}
	
}

?>