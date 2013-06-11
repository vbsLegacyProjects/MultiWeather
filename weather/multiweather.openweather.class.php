<?php
class multiweather_openweather extends multiweather {

	protected $url='http://api.openweathermap.org/data/2.5/weather?lat=%LATITUDE%&lon=%LONGITUDE%&APPID=%KEY%';
	protected $url_city='http://api.openweathermap.org/data/2.5/weather?q=%CITY%&APPID=%KEY%';
	protected $url_forecast='http://api.openweathermap.org/data/2.5/forecast?lat=%LATITUDE%&lon=%LONGITUDE%&APPID=%KEY%';
	protected $url_forecast_city='http://api.openweathermap.org/data/2.5/forecast?q=%CITY%&APPID=%KEY%';

	function load_data(){
		if($this->need_fresh_data()){
			$url=$this->url;
			if(isset($this->config['city']) && strlen($this->config['city'])>0){
				$url=$this->url_city;
			}
			foreach($this->config as $k=>$v){
				$url=str_replace('%'.strtoupper($k).'%',$v,$url);
			}
			$this->last_data=json_decode($this->get_url_contents($url),true);
			
			$url=$this->url_forecast;
			if(isset($this->config['city']) && strlen($this->config['city'])>0){
				$url=$this->url_forecast_city;
			}
			foreach($this->config as $k=>$v){
				$url=str_replace('%'.strtoupper($k).'%',$v,$url);
			}
			$this->last_data['_forecast']=json_decode($this->get_url_contents($url),true);
			$this->last_update=@mktime();
			return true;
		}
		return false;
	}
	
	function get_current_data(){
		$list=array("temp"=>null,"humidity"=>null,"pressure"=>null);
		foreach($list as $k=>$v){
			if($this->last_data['main'][$k]){
				$list[$k]=(float)$this->last_data['main'][$k];
			}
		}
		if($list['temp']!==null){
			$list['temperature']=$list['temp']/10;
		}else{
			$list['temperature']=null;
		}
		if($list['humidity']!==null){
			$list['humidity']=$list['humidity']/100;
		}else{
			$list['humidity']=null;
		}
		unset($list['temp']);
		ksort($list);
		return $list;
	}
	
	function extract_forecast_from_array($values){
		$list=array();
		$list['pressure']=(float)$values['main']['pressure'];
		$list['humidity']=(float)$values['main']['humidity']/100;
		$list['temperature']=(float)$values['main']['temp']/10;
		$list['temperature_min']=(float)$values['main']['temp_min']/10;
		$list['temperature_max']=(float)$values['main']['temp_max']/10;
		return $list;
	}
	
	function get_forecast_data(){
		$forecast_daily=array();
		if(isset($this->last_data['_forecast']['list'])){
			foreach($this->last_data['_forecast']['list'] as $index=>$values){
				$time=@date('Ymd',(int)$values['dt']);
				if($time!==false){
					$forecast_daily[$time]=$this->extract_forecast_from_array($values);
				}
			}
		}
		return array('daily'=>$forecast_daily);
	}

}