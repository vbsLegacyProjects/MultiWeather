<?php
class multiweather {
	
	protected $config;
	public 	  $last_data	= null;
	public	  $last_update	= null;
	
	function __construct($config){
		$this->config=$config;
	}
	
	function get_data(){
		return false;
	}
	
	function get_current_data(){
		return array();
	}
	
	function get_forecast_data(){
		return array();
	}
	
	function need_fresh_data(){
		if(	
			$this->last_data==null
		 || $this->last_update==null
		 || !isset($this->config['delay'])
		 || !is_numeric($this->config['delay']) 
		 || @time()-$this->last_update>$this->config['delay']
		){
			return true;
		}
		return false;
	}
	
	function get_url_contents($url){
		global $ch;
		if(!isset($ch)){
			$ch=curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 			false	);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 2500	);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 	1		);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 	false	);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 	false	); 
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		return curl_exec($ch);
		//return @file_get_contents($url);
	}
	
}