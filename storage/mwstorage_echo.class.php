<?php
class mwstorage_echo {
	function store($key,$info){
		echo $key."\n";
		if(is_array($info)){
			$this->write_array($info,1);
		}
	}
	
	function write_array($info,$head){
		foreach($info as $k=>$v){
			echo str_repeat('  ',$head).$k;
			if(is_array($v)){
				echo "\n";
				$this->write_array($v,$head+1);
			}else{
				echo ' - '.$v."\n";
			}
		}
	}

}