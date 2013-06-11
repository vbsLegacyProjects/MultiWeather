<?php
class multiweather_worldweatheronline extends multiweather {

	protected $url='http://api.worldweatheronline.com/free/v1/weather.ashx?q=%Q%&format=json&extra=utcDateTime&key=%KEY%';

	function load_data(){
		if($this->need_fresh_data()){
			$url=$this->url;
			foreach($this->config as $k=>$v){
				$url=str_replace('%'.strtoupper($k).'%',$v,$url);
			}
			$this->last_data=json_decode($this->get_url_contents($url),true);
			$this->last_update=@mktime();
			return true;
		}
		return false;
	}
	
	function get_current_data(){
		$list=array("temp_C"=>null,"humidity"=>null,"pressure"=>null);
		foreach($list as $k=>$v){
			if($this->last_data['data']['current_condition'][0][$k]){
				$list[$k]=(float)$this->last_data['data']['current_condition'][0][$k];
			}
		}
		if(isset($list['temp_C']) && is_numeric($list['temp_C'])){
			$list['temperature']=$list['temp_C'];
		}else{
			$list['temperature']=null;
		}
		if($list['humidity']!==null){
			$list['humidity']=$list['humidity']/100;
		}
		unset($list['temp_C']);
		ksort($list);
		return $list;
	}
}