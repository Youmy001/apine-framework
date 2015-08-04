<?php

/**
 * Something kind of concrete but still ambiguous to most
 * @author youmy
 *
 */
class Routing{
	
	/**
	 * Something ambiguous returning something ambiguously concrete
	 * @return mixed
	 */
	private static function xml_route(){
		
		$xml_routes=new Parser();
		$xml_routes->load_from_file('routes.xml');
		$request=(isset($_GET['request']))?$_GET['request']:'/index';
		$route_found=false;
		
		$routes=$xml_routes->getElementsByAttributeValue('method', Request::get_request_type());
		
		$str_routes="";
		$found_route=null;
		foreach ($routes as $item){
			
			if($item->nodeType==XML_ELEMENT_NODE){
				foreach($item->childNodes as $attr){
					
		        	if($attr->nodeType==XML_ELEMENT_NODE){
		              
		        		if($attr->tagName=="request"){
		        			if($item->getAttribute('method')==$_SERVER['REQUEST_METHOD']){
		        				$match_route=$item->cloneNode(true);
		        				//print "{$match_route->getElementsByTagName('request')->item(0)->nodeValue}\n";
		        				
		        				$controller=$match_route->getElementsByTagName('controller')->item(0)->nodeValue;
		        				$action=$match_route->getElementsByTagName('action')->item(0)->nodeValue;
		        				
		        				$match=str_ireplace('/','\\/',$match_route->getElementsByTagName('request')->item(0)->nodeValue);
		        				//$match.="(\\/(.*))?";
		        				$match='/^'.$match.'$/';
		        				$replace="/$controller/$action";
		        				if($match_route->getAttribute('args')==true){
		        					$number_args=($match_route->getAttribute('argsnum')!==null)?$match_route->getAttribute('argsnum'):1;
		        					for($i=1;$i<=$number_args;$i++)
		        					$replace.="/$".$i;
		        				}
		        				if(preg_match($match, $request)){
		        					$request=preg_replace($match,$replace,$request);
		        					$found_route=$item->cloneNode(true);
		        					
		        					//print "Found\n";
		        					break;
		        				}
		        			}
		        		}
		        	}
				}
		    }
		    if($found_route!==null){
		    	break;
		    }
		}
		
		return $request;
	}
	
	/**
	 * An ambiguous procedure on ambiguous stuff in order to generate a response whose concreteness is still ambiguous
	 * @return number
	 */
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
					return true;
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
	
	/**
	 * Verifies something ambiguous with a confusing response 
	 * @param mixed $a_route
	 * @return boolean
	 */
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