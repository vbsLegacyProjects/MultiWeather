<?php
class multiweather_hamweather extends multiweather {

	protected $url='http://api.aerisapi.com/observations/%LOCATION%?client_id=%ID%&client_secret=%SECRET%';

	function load_data(){
		if($this->need_fresh_data()){
			$url=$this->url;
			foreach($this->config as $k=>$v){
				$url=str_replace('%'.strtoupper($k).'%',$v,$url);
			}
			$this->last_data=json_decode(file_get_contents($url),true);
			$this->last_update=@mktime();
			return true;
		}
		return false;
	}
	
	function get_current_data(){
		$list=array("tempC"=>null,"humidity"=>null,"pressureMB"=>null);
		foreach($list as $k=>$v){
			if($this->last_data['response']['ob'][$k]){
				$list[$k]=(float)$this->last_data['response']['ob'][$k];
			}
		}
		if($list['tempC']!==null){
			$list['temperature']=$list['tempC'];
		}else{
			$list['temperature']=null;
		}
		if($list['humidity']!==null){
			$list['humidity']=$list['humidity']/100;
		}else{
			$list['humidity']=null;
		}
		if($list['pressureMB']!=null){
			$list['pressure']=$list['pressureMB'];
		}else{
			$list['pressure']=null;
		}
		unset($list['tempC'],$list['pressureMB']);
		ksort($list);
		return $list;
	}

}