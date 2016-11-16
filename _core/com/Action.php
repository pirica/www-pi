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
	
	private $_id_tableeditor;
	
	public $default_code;

	public function __construct($db, $id_app, $code, $id_profile) {
        $this->default_code = 'main';
        
		$this->_db = $db;
		if($id_app == ''){
			$this->_id_app = -1;
		}
		else {
			$this->_id_app = $id_app;
		}
		if($code == ''){
			$this->_code = $this->default_code;
		}
		else {
			$this->_code = $code;
		}
		$this->_id_profile = $id_profile;
		
		$this->_id_app_action = -1;
		$this->_page_title = '';
		$this->_login_required = 1;
		$this->_allowed = 0;
		
		$this->_id_tableeditor = -1;
		
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
	
	public function getEditorId() {
		return $this->_id_tableeditor;
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
			
			$this->_id_tableeditor = -1;
			
			$qry_action = mysqli_query($this->_db, "
				select
					#aa.id_app,
					#aa.code,
					
					aa.id_app_action,
					aa.page_title,
					aa.login_required,
					
					case
						when p.full_access = 1 then 1
						when pa.allowed = 1 and paa.allowed = 1 then 1
						else 0
					end as allowed,
					
					te.id_tableeditor
					
				from t_app_action aa
					join t_profile p on p.id_profile = " . $this->_id_profile . "
					left join t_profile_app pa on pa.id_app = aa.id_app and pa.id_profile = p.id_profile
					left join t_profile_app_action paa on paa.id_app_action = aa.id_app_action and paa.id_profile = p.id_profile
					left join t_tableeditor te on te.action = aa.code and te.id_app = aa.id_app and te.active = 1
					
				where " . 
					($this->_id_app > 0 ? "aa.id_app = " . $this->_id_app : "aa.id_app is null") .
					"
					and aa.code = '" . mysqli_real_escape_string($this->_db, $this->_code) . "'
					and aa.active = 1
					
				order by
					aa.id_app desc
					
				limit 1
				
				");
				
            while($_action = mysqli_fetch_array($qry_action)){
				$this->_id_app_action = $_action['id_app_action'];
				$this->_page_title = $_action['page_title'];
				$this->_login_required = $_action['login_required'];
				$this->_allowed = $_action['allowed'];
				
				$this->_id_tableeditor = $_action['id_tableeditor'];
			}
			
		//}*/
		return $this->_data;
	}
	
	private function setData() {
		mysqli_query($this->_db, "
			insert into t_app_action
			(
				id_app,
				code
			)
			select
				nullif(" . $this->_id_app . ", -1),
				'" . mysqli_real_escape_string($this->_db, $this->_code) . "'
			from t_app_action
			where
				not exists (
					select * from t_app_action where ifnull(id_app,-1) = " . $this->_id_app . " and code = '" . mysqli_real_escape_string($this->_db, $this->_code) . "'
				)
			limit 1, 1
			
			");
			
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