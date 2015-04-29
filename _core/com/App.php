<?php

class App
{
	//public $id;
	//private $id;
	protected $_id;
	protected $_name;
	protected $_info;
	protected $_title;
	
	private $_db;
	protected $_baseurl;
	
	protected $_menudata;
	protected $_menudatasubs;
	
	// boolean to include Video.js player's scripts/css
	public $videojs = false;

	public function __construct($db, $url) {
		$this->_db = $db;
		$this->_baseurl = $url;
		
		if($this->_baseurl == '' || $this->_baseurl == '/'){
			$this->_baseurl = '/';
		}
		else {
			$this->_baseurl = '/' . explode("/", $this->_baseurl)[1];
		}
		
		$this->_menudata = [];
		$this->_menudatasubs = [];
		$this->getMenuData();
		
	}

	public function getId() {
		return $this->_id;
	}
	
	
	public function setName($value) {
		$this->_name = $value;
	}

	public function getName() {
		return $this->_name;
	}
	
	
	public function setTitle($value) {
		$this->_title = $value;
	}

	public function getTitle() {
		return $this->_title;
	}
	
	
	public function getInfo() {
		return $this->_info;
	}
	
	public function getBaseUrl() {
		return $this->_baseurl;
	}
	
	public function getMenuData() {
		if(count($this->_menudata) == 0){
			$qry_apps = $this->_db->prepare("
				select
					a.id_app,
					a.description,
					'' as info,
					a.relative_url,
					a.show_in_overview,
					a.show_in_topmenu,
					a.login_required,
					
					case when ? = a.relative_url then 1 else 0 end as is_current,
                    
                    m.id_menu,
                    lower(ifnull(m.description,'_main')) as menu_code,
                    m.description as menu
					
				from t_app a
                    left join t_menu m on m.id_menu = a.id_menu
				
				order by
					ifnull(m.sort_order, m.id_menu),
					ifnull(a.sort_order, a.id_app)
					
				");
				
			$qry_apps->bind_param('s', $this->_baseurl);
			$qry_apps->execute();
			$qry_apps->store_result();

			//if ($qry_apps->num_rows == 1) {
			$qry_apps->bind_result(
				$id_app,
				$description,
				$info,
				$relative_url,
				$show_in_overview,
				$show_in_topmenu,
				$login_required,
				$is_current,
                $id_menu,
                $menu_code,
                $menu
			);
			
			while ($qry_apps->fetch()) {
                $tmp_menudata = array(
					'id_app' => $id_app,
					'description' => $description,
					'info' => $info,
					'relative_url' => $relative_url,
					'show_in_overview' => $show_in_overview,
					'show_in_topmenu' => $show_in_topmenu,
					'login_required' => $login_required,
					'is_current' => $is_current,
                    'id_menu' => $id_menu,
                    'menu_code' => $menu_code,
                    'menu' => $menu
				);
                
				$this->_menudata[] = $tmp_menudata;
				
                if(count($this->_menudatasubs) == 0 || $this->_menudatasubs[count($this->_menudatasubs)-1]['menu_code'] != $menu_code){
                    $this->_menudatasubs[] = array(
						'is_current' => 0,
						'id_menu' => $id_menu,
						'menu_code' => $menu_code,
						'menu' => $menu,
						'items' => []
					);
                }
                
                $this->_menudatasubs[count($this->_menudatasubs)-1]['items'][] = $tmp_menudata;
				
				if($is_current == 1){
					$this->_id = $id_app;
					$this->_name = $description;
					$this->_info = $info;
					
					$this->_menudatasubs[count($this->_menudatasubs)-1]['is_current'] = 1;
				}
			}
			
		}
		return $this->_menudata;
	}
    
	public function getMenuDataSubs() {
		return $this->_menudatasubs;
    }
	
	/*
	public function __set($var, $value) {
		$this->_data[$var] = $value;
	}
	
	public function __get($var) {
		if(isset($this->_data[$var])) {
			return $this->_data[$var];
		}
	}
	
	
	public function __isset($var) {
		return isset($this->_data[$var]);
	}
	
	public function __unset($var) {
		unset($this->_data[$var]);
	}
	
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
	
	
	protected $_headerScripts;
	
	/**
	 *	Custom javascript / css to include in dsp_header
	 *		(script-tag needs to be included)
	 */
	public function setHeaderScripts($value) {
		$this->_headerScripts .= $value;
	}

	public function getHeaderScripts() {
        if(isset($this->_headerScripts)){
            return $this->_headerScripts;
        }
        else {
            return '';
        }
	}
	
	
	protected $_footerScripts;
	
	/**
	 *	Custom javascript / css to include in dsp_footer
	 *		(script-tag needs to be included)
	 */
	public function setFooterScripts($value) {
		$this->_footerScripts .= $value;
	}

	public function getFooterScripts() {
		if(isset($this->_footerScripts)){
            return $this->_footerScripts;
        }
        else {
            return '';
        }
	}
	
	
	
}

?>