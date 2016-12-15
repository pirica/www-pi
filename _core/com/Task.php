<?php

class Task
{
	
	protected $_data;
	
	private $_db;
	private $_id_app;
	
	private $_id_task;
	private $_name;
	private $_is_running;
	

	public function __construct($db, $id_app, $name) {
		$this->_db = $db;
		if($id_app == ''){
			$this->_id_app = -1;
		}
		else {
			$this->_id_app = $id_app;
		}
		$this->_name = $name;
		
		$this->_id_task = -1;
		$this->_is_running = 0;
		
		$this->getData();
		
		// task not found
		if($this->_id_task == -1){
			$this->setData();
			//$this->getData();
		}
		
		// task still not found or could not add
		/*if($this->_id_task == -1){
			trigger_error('task ' . $id_app . ':"' . $name . '" not defined!', E_USER_ERROR);
		}*/
		
	}
	
	public function getName() {
		return $this->_name;
	}
	
	public function getId() {
		return $this->_id_task;
	}
	
	public function getIsRunning() {
		return ($this->_is_running == 1);
	}
	
	public function setIsRunning($value) {
		if($value)
		{
			mysqli_query($this->_db, "
				update users.t_task
				set
					is_running = 1,
					date_last_run = now()
				
				where
					name = '" . mysqli_real_escape_string($this->_db, $this->_name) . "'
					and is_running = 0
				");
		}
		else
		{
			mysqli_query($this->_db, "
				update users.t_task
				set
					is_running = 0,
					date_last_completed = now()
				
				where
					name = '" . mysqli_real_escape_string($this->_db, $this->_name) . "'
					and is_running = 1
				");
		}
	}
	
	private function getData() {
		//if(count($this->_data) == 0){
			$this->_data = [];
			
			$this->_id_task = -1;
			//$this->_name = '';
			$this->_is_running = 0;
			
			$qry_task = mysqli_query($this->_db, "
				select
					t.id_task,
					t.name,
					t.is_running
					
				from users.t_task t
					
				where 
					t.id_app = " . $this->_id_app . "
					and t.name = '" . mysqli_real_escape_string($this->_db, $this->_name) . "'
					
				limit 1
				
				");
				
            while($_task = mysqli_fetch_array($qry_task)){
				$this->_id_task = $_task['id_task'];
				//$this->_name = $_task['name'];
				$this->_is_running = $_task['is_running'];
				
			}
			
		//}*/
		return $this->_data;
	}
	
	private function setData() {
		mysqli_query($this->_db, "
			insert into users.t_task
			(
				id_app,
				name
			)
			select
				" . $this->_id_app . ",
				'" . mysqli_real_escape_string($this->_db, $this->_name) . "'
			from users.t_task
			where
				not exists (
					select * from users.t_task where id_app = " . $this->_id_app . " and name = '" . mysqli_real_escape_string($this->_db, $this->_name) . "'
				)
			limit 0, 1
			
			");
			
	}
	
}

?>