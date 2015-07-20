<?php

class Routing{
	
	private static $routes = array(
		"/index"			=> "/home/index",
		"/login/restore"	=> "/session/restore",
		"/login"			=> "/session/login",
		"/logout"			=> "/session/logout",
		"/register"		=> "/session/register",
		"/redirect"		=> "/session/redirect"
	);
	
	private static function xml_route(){
		$xml_routes=new Parser();
		$xml_routes->load_from_file('routes.xml');
		$request=(isset($_GET['request']))?$_GET['request']:'/index';
		$route_found=false;
		
		$routes=$xml_routes->getElementsByAttributeValue('method', session()->get_session_request_type());
		
		$str_routes="";
		$found_route=null;
		foreach ($routes as $item){
			print 'route<br>';
			//print var_dump($item->hasChildNodes());
			if($item->nodeType==XML_ELEMENT_NODE){
				foreach($item->childNodes as $attr){
					//print "Child";
		        	if($attr->nodeType==XML_ELEMENT_NODE){
		              //print $attr->tagName.": ".$attr->nodeValue."<br>";
		        		if($attr->tagName=="request"){
		        			print $request."=".$attr->nodeValue."<br>";
		        			var_dump(strpos($request,$attr->nodeValue));
		        			print '<br>';
		        			if(strpos($request,$attr->nodeValue)===0&&$item->getAttribute('method')==$_SERVER['REQUEST_METHOD']){
		        				$found_route=$item->cloneNode(true);
		        				print "matched<br>";
		        				break;
		        			}
		        		}
		        	}
				}
		       //print "----------<br>";
		    }
		    if($found_route!==null){
		    	break;
		    }
		    //print $item->tagName."<br>";
		    //print "----------<br>";
		}
		if($found_route!==null){
			//print $found_route->getElementsByTagName('controller')->item(0)->nodeValue.'/'.$found_route->getElementsByTagName('action')->item(0)->nodeValue;
			$controller=$found_route->getElementsByTagName('controller')->item(0)->nodeValue;
			$action=$found_route->getElementsByTagName('action')->item(0)->nodeValue;
			
			//$request_args=substr($_GET['request'], $found_route->getElementsByTagName('request')->nodeValue);
			$request=preg_replace($found_route->getElementsByTagName('request')->nodeValue,"$controller/$action",$request);
		}else{
			
		}
		
		die();
	} 
	
	public static function route(){
		//self::xml_route();
		//print $_GET['request'];
		$request=(isset($_GET['request']))?$_GET['request']:'/index';
		$route_found=false;
		
		//print ucfirst($controller)."Controller->".$action.'()';*/
		$vanilla_route_found=self::check_route($request);
		
		if(!$vanilla_route_found){
			foreach(self::$routes as $match => $replace){
				$match=str_ireplace('/','\\/',$match);
				$match='/^'.$match.'$/';
				if(preg_match($match, $request)){
					$request=preg_replace($match,$replace,$request);
					$route_found=true;
					break;
				}
			}
		}
		
		$args=explode("/",$request);
		array_shift($args);
		if(count($args)>1){
			$controller=$args[0];
			array_shift($args);
			$action=$args[0];
			array_shift($args);
		}else{
			$controller=$args[0];
			array_shift($args);
			$action="index";
		}
		
		// Add post arguments to args array
		if($_SERVER['REQUEST_METHOD']!="GET"){
			$args=array_merge($args,$_POST);
		}
		if(!empty($_FILES)){
			$args=array_merge($args,array("uploads" => $_FILES));
		}
		
		try{
			if(file_exists('controllers/'.$controller.'_controller.php')){
				require_once('controllers/'.$controller.'_controller.php');
				$maj_controller=ucfirst($controller).'Controller';
				if(method_exists($maj_controller,$action)){
					//print "Found";
					$controller=ucfirst($controller).'Controller';
					$controller=new $controller();
					$controller->$action($args);
					return 0;
				}
			}
			if($route_found){
				//print "Gone";
				$controller=new ErrorController();
				$controller->gone();
			}else{
				//print "Not Found";
				$controller=new ErrorController();
				$controller->notfound();
			}
		}catch(Exception $e){
			//print "Error";
			$controller=new ErrorController();
			$controller->server();
		}
		
		//return array($controller,$action, $args);
	}
	
	private static function check_route($a_route){
		$args=explode("/",$a_route);
		
		array_shift($args);
		if(count($args)>1){
			$controller=$args[0];
			array_shift($args);
			$action=$args[0];
			array_shift($args);
		}else{
			$controller=$args[0];
			array_shift($args);
			$action="index";
		}
		
		try{
			if(file_exists('controllers/'.$controller.'_controller.php')){
				require_once('controllers/'.$controller.'_controller.php');
				$maj_controller=ucfirst($controller).'Controller';
				if(method_exists($maj_controller,$action)){
					//print "Found";
					return true;
				}
			}
			return false;
		}catch(Exception $e){
			//print "Error";
			$controller=new ErrorController();
			$controller->server();
			die();
		}
	} 
}