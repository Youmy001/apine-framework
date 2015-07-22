<?php

class Routing{
	
	private static $routes = array(
		"/index"			=> "/home/index",
		"/about"			=> "/home/about",
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
			
			if($item->nodeType==XML_ELEMENT_NODE){
				foreach($item->childNodes as $attr){
					
		        	if($attr->nodeType==XML_ELEMENT_NODE){
		              
		        		if($attr->tagName=="request"){
		        			if(strpos($request,$attr->nodeValue)===0&&$item->getAttribute('method')==$_SERVER['REQUEST_METHOD']){
		        				$found_route=$item->cloneNode(true);
		        				break;
		        			}
		        		}
		        	}
				}
		    }
		    if($found_route!==null){
		    	break;
		    }
		}
		if($found_route!==null){
			
			$controller=$found_route->getElementsByTagName('controller')->item(0)->nodeValue;
			$action=$found_route->getElementsByTagName('action')->item(0)->nodeValue;
			
			$match=str_ireplace('/','\\/',$found_route->getElementsByTagName('request')->item(0)->nodeValue);
			$match.="(\\/(.*))?";
			$match='/^'.$match.'$/';
			
			$replace="/$controller/$action";
			$request=preg_replace($match,$replace,$request);
			
		}
		
		return $request;
	}

	public static function route(){
		$request=(isset($_GET['request']))?$_GET['request']:'/index';
		$route_found=false;
		
		$vanilla_route_found=self::check_route($request);
		
		if(!$vanilla_route_found){
			$xml_request=self::xml_route();
			if($xml_request!==$request){
				$route_found=true;
				$request=$xml_request;
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
			require_once('controllers/error_controller.php');
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