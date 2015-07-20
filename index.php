<?php
ini_set('display_errors','On');
ini_set('include_path', realpath(dirname(__FILE__)));
error_reporting(E_ALL | E_STRICT);

if(!function_exists('str_split_unicode')){
	function str_split_unicode($str, $l = 0) {
	    if ($l > 0) {
	        $ret = array();
	        $len = mb_strlen($str, "UTF-8");
	        for ($i = 0; $i < $len; $i += $l) {
	            $ret[] = mb_substr($str, $i, $l, "UTF-8");
	        }
	        return $ret;
	    }
	    return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
	}
}
$dir=realpath(dirname(__FILE__)).'/lib/';

function folder_files($dir,$root=true){
	$array=array();
	if (is_dir($dir)) {
		if(!$root){
			//print($dir);
			// Extract directories
			$a_dir=array();
			$a_file=array();
			foreach(scandir($dir) as $file){
				if ($file != "." && $file != "..") {
					if(is_dir($dir . $file . '/')){
						$a_dir[]=$dir.$file;
					}else{
						$a_file[]=$dir.$file;
					}
				}
			}
			/*array_push($array, $a_dir);
			array_push($array, $a_file);*/
			foreach ($a_dir as $file){
				if ($file != "." && $file != "..") {
					$dir_array=folder_files($file . '/',false);
					//$array[$dir . $file . '/']=folder_files($dir . $file . '/');
					//array_push($array,$dir_array);
					foreach ($dir_array as $dir_file){
						$array[]=$dir_file;
					}
				}
			}
			
			foreach ($a_file as $file){
				$array[]=$file;
			}
			
		}else{
			foreach(scandir($dir) as $file){
				if ($file != "." && $file != "..") {
					if(is_dir($dir . $file . '/')){
						$dir_array=folder_files($dir . $file . '/',false);
						//$array[$dir . $file . '/']=folder_files($dir . $file . '/');
						//array_push($array,$dir_array);
						foreach ($dir_array as $dir_file){
							$array[]=$dir_file;
						}
					}else{
						//echo " ( filename: $dir$file ; filetype: " . filetype($dir . $file) . " ) ,\n";
						//require_once("$dir$file");
						$array[]=$dir.$file;
					}
				}
			}
		}
	}
	return $array;
}

foreach (folder_files($dir) as $file){
	require_once $file;
}


function session(){
	// Start Session
	static $session;
	
	if($session==null){
		$session=new ApineSession();
		//print 'session ';
	}
	return $session;
}

Routing::route();
?>