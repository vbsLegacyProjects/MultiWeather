<?php
class mwstorage_file {
	function store($key,$info){
		if(defined('SCRIPT_DIR')){
			$f=fopen(SCRIPT_DIR.'output.txt','a');
		}else{
			$f=fopen('output.txt','a');
		}
		fwrite($f,$key."\n");
		if(is_array($info)){
			$this->write_array($f,$info,1);
		}
		fclose($f);
	}
	
	function write_array($f,$info,$head){
		foreach($info as $k=>$v){
			fwrite($f,str_repeat(' ',$head).$k);
			if(is_array($v)){
				fwrite($f,"\n");
				$this->write_array($f,$v,$head+1);
			}else{
				fwrite($f,' - '.$v."\n");
			}
		}
	}

}