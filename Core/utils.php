<?php

use Apine\MVC\URLHelper;
use Apine\Core\Config;
use Apine\Session\SessionManager;
use Apine\Translation\ApplicationTranslator;
/**
 * #@+
 * Constants
 */
define('APINE_MODE_DEVELOPMENT', 5);
define('APINE_MODE_PRODUCTION', 6);
define('APINE_PROTOCOL_HTTP', 0);
define('APINE_PROTOCOL_HTTPS', 1);
define('APINE_PROTOCOL_DEFAULT', 2);
define('APINE_ROUTES_JSON', 25);
define('APINE_ROUTES_XML', 26);
define('APINE_RUNTIME_API', 16);
define('APINE_RUNTIME_APP', 17);
define('APINE_RUNTIME_HYBRID', 18);
define('APINE_SESSION_ADMIN', 77);
define('APINE_SESSION_USER', 65);
define('APINE_SESSION_GUEST', 40);
define('APINE_SESSION_DELETED', 10);

/**
 * A split method that supports unicode characters
 *
 * @param string $str        
 * @param number $l        
 * @return string
 */
function str_split_unicode ($str, $l = 0) {

	if ($l > 0) {
		
		$ret = array();
		$len = mb_strlen($str, "UTF-8");
		
		for ($i = 0;$i < $len;$i += $l) {
			$ret[] = mb_substr($str, $i, $l, "UTF-8");
		}
		
		return $ret;
	}
	
	return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);

}

/**
 * Check if a string is a valid ISO 8601 Timestamp
 *
 * @param string $timestamp        
 * @return boolean Source :
 *         http://community.sitepoint.com/t/check-whether-the-string-is-timestamp/4468/19
 */
function is_timestamp ($timestamp) {

	return (bool) preg_match('/^(?:(?P<year>[-+]\\d{4,}|\\d{4})(?:(?:-(?P<month>1[012]|0[1-9])(?:-(?P<day>3[01]|[12]\\d|0[1-9]))?)|(?:-[Ww](?P<yearweek>5[0-3]|[1-4]\\d|0[1-9])(?:-(?P<weekday>[1-7]))?)|(?:-(?P<yeardays>36[0-6]|3[0-5]\\d|[12]\\d{2}|0[1-9]\\d|00[1-9])))?)(?:(?:[Tt]| +)(?P<hour>2[0-4]|[01]\\d)(?:\\:(?P<minutes>[0-5]\\d)(?:\\:(?P<seconds>60|[0-5]\\d))?)?(?P<fraction>[,.][\\d.]+)?\\s*(?P<timezone>Z|[+-](?:1[0-4]|0[0-9])(?:\\:?[0-5]\\d)?)?)?$/', $timestamp);

}


/**
 * Check if a string is a valid JSON string
 * 
 * @param string $string
 * @return boolean
 */
function is_json($string) {
	
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
	
}



/**
 * Loads all files recursively a user defined module in the model/
 * directory
 *
 * @param string $module_name
 *        Name of the folder of the module
 */
function apine_load_module ($module_name) {

	return Apine\Autoloader::load_module($module_name);

}

/**
 * Write strings in a configuration file in INI format
 *
 * Source:
 * http://stackoverflow.com/questions/1268378/create-ini-file-write-values-in-php
 */
function write_ini_file ($assoc_arr, $path, $has_sections = FALSE) {

	$content = "";
	
	if ($has_sections) {
		foreach ($assoc_arr as $key=>$elem) {
			$content .= "[" . $key . "]\n";
			
			foreach ($elem as $key2=>$elem2) {
				if (is_array($elem2)) {
					for ($i = 0;$i < count($elem2);$i++) {
						$content .= "\t" . $key2 . "[] = \"" . $elem2[$i] . "\"\n";
					}
				} else if ($elem2 == "") {
					$content .= "\t" . $key2 . " = \n";
				} else {
					$content .= "\t" . $key2 . " = \"" . $elem2 . "\"\n";
				}
			}
		}
	} else {
		foreach ($assoc_arr as $key=>$elem) {
			if (is_array($elem)) {
				for ($i = 0;$i < count($elem);$i++) {
					$content .= "\t" . $key . "[] = \"" . $elem[$i] . "\"\n";
				}
			} else if ($elem == "") {
				$content .= "\t" . $key . " = \n";
			} else {
				$content .= "\t" . $key . " = \"" . $elem . "\"\n";
			}
		}
	}
	
	if (!$handle = fopen($path, 'w')) {
		return false;
	}
	
	$success = fwrite($handle, $content);
	fclose($handle);
	return $success;

}

/**
 * Compute a ratio from a multiplier
 *
 * @param double $n
 *        Ratio multiplier
 * @param real $tolerance
 *        Precision level of the procedure
 * @return string
 */
function float2rat ($n, $tolerance = 1.e-6) {

	$h1 = 1;
	$h2 = 0;
	$k1 = 0;
	$k2 = 1;
	$b = 1 / $n;
	
	do {
		$b = 1 / $b;
		$a = floor($b);
		$aux = $h1;
		$h1 = $a * $h1 + $h2;
		$h2 = $aux;
		$aux = $k1;
		$k1 = $a * $k1 + $k2;
		$k2 = $aux;
		$b = $b - $a;
	} while (abs($n - $h1 / $k1) > $n * $tolerance);
	
	return "$h1/$k1";

}

/**
 * Verify if exec is disabled
 *
 * @author Daniel Convissor
 *        
 *         http://stackoverflow.com/questions/3938120/check-if-exec-is-disabled
 */
function is_exec_available () {

	static $available;
	
	if (!isset($available)) {
		$available = true;
		if (ini_get('safe_mode')) {
			$available = false;
		} else {
			$d = ini_get('disable_functions');
			$s = ini_get('suhosin.executor.func.blacklist');
			if ("$d$s") {
				$array = preg_split('/,\s*/', "$d,$s");
				if (in_array('exec', $array)) {
					$available = false;
				}
			}
		}
	}
	
	return $available;

}

function recurse_copy ($src, $dst) {

	$dir = opendir($src);
	@mkdir($dst, 0777);
	@chmod($dst, 0777);
	
	while (false !== ($file = readdir($dir))) {
		if (($file != '.') && ($file != '..')) {
			if (is_dir($src . '/' . $file)) {
				recurse_copy($src . '/' . $file, $dst . '/' . $file);
			} else {
				copy($src . '/' . $file, $dst . '/' . $file);
				chmod($dst . '/' . $file, 0777);
			}
		}
	}
	
	closedir($dir);

} 

/**
 * Calculate the total execution time
 * of the request up to now
 * 
 * @return string
 */
function apine_execution_time () {

	global $before;
	$after = microtime(true) * 1000;
	$time = number_format($after - $before, 1);
	
	return $time;

}

/**
 * Redirect to another end point of the application
 * using a full query string
 * 
 * @param string $request
 * @param string $protocol
 */
function apine_internal_redirect ($a_request, $a_protocol = APINE_PROTOCOL_DEFAULT) {
	
	$protocol = (isset(Apine\Core\Request::server()['SERVER_PROTOCOL']) ? Apine\Core\Request::server()['SERVER_PROTOCOL'] : 'HTTP/1.0');
	
	if ($a_request == Apine\Core\Request::get()['request']) {
		header($protocol . ' 302 Moved Temporarily');
	}
	header('Location: ' . Apine\MVC\URLHelper::path($a_request, $a_protocol));
	
}

/**
 * Return the instance of the Apine Application
 * 
 * @return Apine\Application\Application
 */
function apine_application () {
	
	return Apine\Application\Application::get_instance();
	
}

/**
 * Return the instance of the Apine Config
 * 
 * @return Apine\Application\ApplicationConfig
 */
function apine_app_config () {

	return Apine\Application\Config::get_instance();

}

/**
 * Return the instance of the Session Manager
 * 
 * @return Apine\Session\SessionManager
 */
function apine_session () {

	return Apine\Session\SessionManager::get_instance();

}

/**
 * Return the instance of the Application Translator
 * 
 * @return Apine\Application\ApplicationTranslator
 */
function apine_app_translator () {

	return Apine\Application\Translator::get_instance();

}

/**
 * Return the instance of the URL Helper
 * 
 * @return Apine\MVC\URLHelper
 */
function apine_url_helper () {
	
	return Apine\MVC\URLHelper::get_instance();
	
}

/**
 * Return the extension from a file name
 * 
 * @param string $a_file_path
 * @return string
 */
function file_extension ($a_file_path) {
	
	$name = basename($a_file_path);
	$dot_pos = strpos($name, ".");
	
	if ($dot_pos > 0) {
		$extension = substr($name, $dot_pos + 1);
	} else {
		$extension = $name;
	}
	
	return $extension;
	
}

/**
 * Export XML routes in a JSON Format
 * 
 * @param string $file
 * @return array
 */
function apine_export_routes ($file) {
	$xml_routes = new Apine\XML\Parser();
	$xml_routes->load_from_file($file);
	$routes = array();
	
	foreach ($xml_routes->getElementByTagName('route') as $item) {
		if ($item->nodeType == XML_ELEMENT_NODE) {
			$nodes = array();
			$method = "";
			$request = "";
				
			foreach ($item->attributes as $attr) {
				if ($attr->nodeType == XML_ATTRIBUTE_NODE) {
					if ($attr->nodeName === 'method') {
						$method = $attr->nodeValue;
					} elseif ($attr->nodeName === 'args') {
						$nodes['args'] = (bool)$attr->nodeValue;
					} else {
						$nodes[$attr->nodeName] = $attr->nodeValue;
					}
				}
			}
				
			foreach ($item->getElementsByTagName('*') as $node) {
				if ($node->nodeType == XML_ELEMENT_NODE) {
					if ($node->nodeName === 'request') {
						$request = $node->nodeValue;
					} else {
						$nodes[$node->nodeName] = $node->nodeValue;
					}
				}
			}
				
			$routes[$request][$method] = $nodes;
		}
	}
	
	return $routes;
}