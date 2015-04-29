<?php

class Settings
{
	
	protected $_data;
	
	private $_db;
	private $_id_app;
	private $_count = 0;

	public function __construct($db, $id_app) {
		$this->_db = $db;
		$this->_id_app = $id_app;
		
		$this->getData();
		
	}
	
	public function getIdApp(){
		return $this->_id_app;
	}
	
	public function getData() {
		//if(count($this->_data) == 0){
			$this->_count = 0;
			$this->_data = [];
			
			$qry_settings = $this->_db->prepare("
				select
					s.id_app,
					s.code,
					s.value,
					s.description,
					s.editable,
					s.edittype,
					s.extra,
					s.category,
					s.sort_order,
					s.tooltip
					
				from t_setting s
				where
					s.id_app = ?
					and s.active = 1
					
				order by
					ifnull(s.sort_order, s.id_setting)
					
				");
				
			$qry_settings->bind_param('i', $this->_id_app);
			$qry_settings->execute();
			$qry_settings->store_result();

			//if ($qry_settings->num_rows == 1) {
			$qry_settings->bind_result(
				$id_app,
				$code,
				$value,
				$description,
				$editable,
				$edittype,
				$extra,
				$category,
				$sort_order,
				$tooltip
			);
			
			while ($qry_settings->fetch()) {
				$this->_count++;
				$this->_data[] = array(
					'id_app' => $id_app,
					'code' => $code,
					'value' => $value,
					'description' => $description,
					'editable' => $editable,
					'edittype' => $edittype,
					'extra' => $extra,
					'category' => $category,
					'sort_order' => $sort_order,
					'tooltip' => $tooltip
				);
				
			}
			
		//}*/
		return $this->_data;
	}
	
	private function setData($code, $value) {
		/*
		$qry_setdata = $this->_db->prepare("
			insert into t_setting
			(
				id_app,
				code,
				value
			)
			values
			(
				?,
				?,
				?
			)
			");
			
		$qry_setdata->bind_param('iss', $this->_id_app, $code, $value);
		$qry_setdata->execute();
		// */
	}
	
	public function val($code, $defaultvalue='') {
		$return_value = $defaultvalue;
		$value_found = 0;
		
		for($i=0; $i<$this->_count; $i++) {
			if($this->_data[$i]['code'] == $code){
				$return_value = $this->_data[$i]['value'];
				$value_found = 1;
			}
		}
		
		if($value_found == 0){
			$this->setData($code, $defaultvalue);
			
			//$this->getData();
			$this->_count++;
			$this->_data[] = array(
				'id_app' => $this->_id_app,
				'code' => $code,
				'value' => $defaultvalue,
				'description' => '',
				'editable' => 0,
				'edittype' => '',
				'extra' => '',
				'category' => '',
				'sort_order' => -1,
				'tooltip' => ''
			);
		}
		return $return_value;
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
		$output = '<table border="1">'.PHP_EOL;
		
		foreach($this->_rows as $row) {
			$output .= $row;
		}
		
		$output .= '</table>'.PHP_EOL;
		
		return $output;
	} 
	
	*/
	
	public function _debug() {
		print_r($this->_data);
	}
	
}

?>