<?php
set_time_limit(0);
if(!defined('SCRIPT_DIR')){
	define('SCRIPT_DIR',str_replace('\\','/',dirname(__FILE__)).'/');
}
$config_modified=filemtime(SCRIPT_DIR.'config.php');
if(is_file('config.my.php')){
	include(SCRIPT_DIR.'config.my.php');
}else{
	include(SCRIPT_DIR.'config.php');
}
require_once(SCRIPT_DIR.'storage/mwstorage_'.$storage_class.'.class.php');
require_once(SCRIPT_DIR.'weather/multiweather.class.php');
require_once(SCRIPT_DIR.'lib/mwmath.class.php');
$stc='mwstorage_'.$storage_class;
$storage=new $stc;
unset($stc);
$source=array();
foreach($weather_api_list as $k=>$v){
	if($v['use']==true){
		unset($v['use']);
		require_once(SCRIPT_DIR.'weather/multiweather.'.$k.'.class.php');
		$class='multiweather_'.$k;
		$source[$k]=new $class($v);
	}
}

if(!isset($loops)){ $loops=null; }

while($loops==null || $loops>0){
	$modified=false;
	foreach($source as $sourceIndex=>$sourceObject){
		if($source[$sourceIndex]->load_data()===true){
			$modified=true;
		}
	}
	if($modified==true){
		foreach(array('current','forecast') as $typeOfDataIndex=>$typeOfData){
			$current=array();
			$calculated=array();
			$get_function='get_'.$typeOfData.'_data';
			foreach($source as $sourceIndex=>$sourceObject){
				$current[$sourceIndex]=$source[$sourceIndex]->$get_function();
				if($typeOfData=='current'){
					foreach($current[$sourceIndex] as $key=>$value){
						if(is_numeric($value)){
							if(!isset($calculated[$key])){
								$calculated[$key]=array();
							}
							$calculated[$key][]=$value;
						}
					}
				}
			}
			$data=array();
			$data['timestamp']=@time();
			$data['values']=$current;
			if($typeOfData=='current'){
				$averages=array();
				$median=array();
				foreach($calculated as $datatype=>$values){
					$median[$datatype]=mwmath::median($values);
					$averages[$datatype]=array_sum($values)/count($values);
				}
				$data['separated_values']=$calculated;
				$data['median']=$median;
				$data['averages']=$averages;
			}else{
				
				$estimates=array('daily'=>array());
				
				foreach(array('daily') as $forecastTypeIndex=>$forecastType){
					//echo $forecastType."\n\n";
					foreach($current as $ksource=>$info){
						foreach($info as $kDate=>$vData){
							if($kDate==$forecastType){
								foreach($vData as $thetime=>$thevalues){
									if(!isset($estimates[$forecastType][$thetime])){
										$estimates[$forecastType][$thetime]=array();
									}
									$estimates[$forecastType][$thetime][]=$thevalues;
								}
							}
						}
					}
				}
				
				$calculated=array();
				foreach($estimates as $forecastType=>$v1){
					if(is_array($v1) && count($v1)>0){
						foreach($v1 as $date=>$values){
							if(count($values)>0){
								$setKeys=array();
								foreach($values as $valueIndex=>$set){
									foreach($set as $setKey=>$setValue){
										
										if(!isset($calculated[$date])){
											$calculated[$date]=array();
										}
										if(!isset($calculated[$date][$setKey])){
											$calculated[$date][$setKey]=array();
										}
										$calculated[$date][$setKey][]=$setValue;
									}									
								}
							}
						}
					}
				}
				
				$median=array();
				$averages=array();
				foreach($calculated as $date=>$sets){
					foreach($sets as $type=>$list){
						if(count($list)==1){
							$this_median=$list[0];
							$this_average=$list[0];
						}else{
							$this_median=mwmath::median($list);
							$this_average=array_sum($list)/count($list);
						}
						if(!isset($median[$date])){$median[$date]=array();}
						$median[$date][$type]=$this_median;
						if(!isset($averages[$date])){$averages[$date]=array();}
						$averages[$date][$type]=$this_average;
						
					}
				}
				$data['separated_values']=$calculated;
				$data['median']=$median;
				$data['averages']=$averages;
			}
			$storage->store('weather_'.$typeOfData,$data);
			unset($data,$averages,$median,$calculated,$current);
		}
	}
	
	//Configuration update script
	$config_check=filemtime(SCRIPT_DIR.'config.php');
	if($config_check!==$config_modified){
		$config_modified=$config_check;
		include(SCRIPT_DIR.'config.php');
	}
	echo ";\n";

	if(is_numeric($loops)){
		$loops--;
	}
	if($loops==null || (is_numeric($loops) && $loops>0)){
		sleep($main_delay);
	}
}
