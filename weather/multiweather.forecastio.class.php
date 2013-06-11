<?php
class multiweather_forecastio extends multiweather {

	protected $url='https://api.forecast.io/forecast/%KEY%/%LATITUDE%,%LONGITUDE%?units=si&exclude=alerts,flags';

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
		if(isset($this->last_data['currently'])){
			return $this->extract_weather_from_array($this->last_data['currently']);
		}
		return array();
	}

	function extract_weather_from_array($array){
		$list=array(
			"temperature"=>null,"humidity"=>null,"pressure"=>null
			,'precipProbability'=>null,'precipIntensity'=>null
			);
		foreach($list as $k=>$v){
			if(isset($array[$k])){
				$list[$k]=(float)$array[$k];
			}
		}
		if(isset($list['precipProbability'])){
			$list['precip_probability']=$list['precipProbability'];
			unset($list['precipProbability']);
		}
		if(isset($list['precipIntensity'])){
			$list['precip_intensity']=$list['precipIntensity'];
			unset($list['precipIntensity']);
		}
		ksort($list);
		return $list;
	}

	function get_forecast_data(){
		$forecast_hourly=array();
		if(isset($this->last_data['hourly']['data'])){
			foreach($this->last_data['hourly']['data'] as $index=>$values){
				$time=@date('Ymdh',(int)$values['time']);
				if($time!==false){
					$forecast_hourly[$time]=$this->extract_weather_from_array($values);
				}
			}
		}
		$forecast_daily=array();
		if(isset($this->last_data['daily']['data'])){
			foreach($this->last_data['daily']['data'] as $index=>$values){
				$time=@date('Ymd',(int)$values['time']);
				if($time!==false){
					$forecast_hourly[$time]=$this->extract_weather_from_array($values);
				}
			}
		}
		return array(
			 'hourly'	=> $forecast_hourly
			,'daily'	=> $forecast_daily
		);
	}

}