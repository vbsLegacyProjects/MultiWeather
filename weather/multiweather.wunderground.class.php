<?php
class multiweather_wunderground extends multiweather {

	protected $url='http://api.wunderground.com/api/%KEY%/geolookup/conditions/q/IA/%LOCATION%.json';
	protected $url_forecast='http://api.wunderground.com/api/%KEY%/forecast/conditions/q/IA/%LOCATION%.json';

	function load_data(){
		if($this->need_fresh_data()){
			$url=$this->url;
			foreach($this->config as $k=>$v){
				$url=str_replace('%'.strtoupper($k).'%',$v,$url);
			}
			$this->last_data=json_decode($this->get_url_contents($url),true);
			
			$url=$this->url_forecast;
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
		$list=array("temp_c"=>null,"relative_humidity"=>null,"pressure_mb"=>null);
		foreach($list as $k=>$v){
			if($this->last_data['current_observation'][$k]){
				$list[$k]=(float)$this->last_data['current_observation'][$k];
			}
		}
		if(isset($list['temp_c']) && is_numeric($list['temp_c'])){
			$list['temperature']=$list['temp_c'];
		}else{
			$list['temperature']=null;
		}
		if(isset($list['relative_humidity']) && is_numeric($list['relative_humidity'])){
			$list['humidity']=$list['relative_humidity']/100;
		}else{
			$list['humidity']=null;
		}
		if(isset($list['pressure_mb']) && is_numeric($list['pressure_mb'])){
			$list['pressure']=$list['pressure_mb'];
		}else{
			$list['pressure']=null;
		}
		unset($list['temp_c'],$list['relative_humidity'],$list['pressure_mb']);
		ksort($list);
		return $list;
	}
	
	function extract_weather_from_array($values){
		$list=array();
		$list['precip_probability']=0;
		if((float)$values['qpf_day']+(float)$values['qpf_night']>0.1 
		|| (float)$values['snow_day']+(float)$values['snow_night']>0.1
		){
			$list['precip_probability']=0.8;
		}		
		$list['humidity']=(float)$values['avehumidity']/100;
		$list['temperature_min']=(float)$values['low']['celsius'];
		$list['temperature_max']=(float)$values['high']['celsius'];
		$list['temperature']=($list['temperature_min']+$list['temperature_max'])/2;
		return $list;
	}
	
	function get_forecast_data(){
		$forecast_daily=array();
		if(isset($this->last_data['_forecast']['forecast']['simpleforecast']['forecastday']) 
		  && is_array($this->last_data['_forecast']['forecast']['simpleforecast']['forecastday'])){
			foreach($this->last_data['_forecast']['forecast']['simpleforecast']['forecastday'] as $index=>$values){
				$time=@date('Ymd',(int)$values['date']['epoch']);
				if($time!==false){
					$forecast_daily[$time]=$this->extract_weather_from_array($values);
				}
			}
		}
		return array('daily'=>$forecast_daily);
	}

}