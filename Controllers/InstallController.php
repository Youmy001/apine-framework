<?php

namespace Apine\Controllers\System;

use Apine\Core as Core;
use Apine\MVC as MVC;
use Apine\Core\Request;
use Apine\Core\Database;
use Apine\Exception\GenericException;
use Apine\Exception\DatabaseException;
use Apine\Application\Application;

class InstallController extends MVC\Controller {

	/**
	 *
	 * @return MVC\HTMLView
	 */
	public function index () {

		$parent = dirname(dirname(__FILE__));
		$parent_name = basename($parent);
		$project = dirname($parent);
		
		if (file_exists($project . '/.htaccess') && file_exists($project . '/config.ini') && (file_exists($project . '/routes.json') || file_exists($project . '/routes.xml'))) {
			apine_internal_redirect('/home');
		}
		
		$parent = dirname(dirname(__FILE__));
		$parent_name = basename($parent);
		$project = dirname($parent);
		
		if (!file_exists($project . '/.htaccess')) {
			// Load .htaccess templace
			ob_start();
			include_once $parent . '/Installation/htaccess_install.php';
			$content = ob_get_contents();
			ob_end_clean();
			
			// Create htaccess
			$file = fopen($project . '/.htaccess', 'x+');
			fwrite($file, $content);
			fclose($file);
		}
		
		if (Core\Request::is_get()) {
			$zones = timezone_identifiers_list();
			
			foreach ($zones as $zone) {
				$zone = explode('/', $zone); // 0 => Continent, 1 => City
				                             
				// Only use "friendly" continent names
				if ($zone [0] == 'Africa' || $zone [0] == 'America' || $zone [0] == 'Antarctica' || $zone [0] == 'Arctic' || $zone [0] == 'Asia' || $zone [0] == 'Atlantic' || $zone [0] == 'Australia' || $zone [0] == 'Europe' || $zone [0] == 'Indian' || $zone [0] == 'Pacific') {
					if (isset($zone [1]) != '') {
						$locations [$zone [0]] [$zone [0] . '/' . $zone [1]] = str_replace('_', ' ', $zone [1]); // Creates array(DateTimeZone => 'Friendly name')
					}
				}
			}
			
			include $parent . '/Installation/locales.php';
			
			$this->_view->set_title("Install APIne Framework");
			$this->_view->set_layout($parent . '/Installation/views/layout');
			$this->_view->set_view($parent . '/Installation/views/index');
			$this->_view->set_param('timezones', $locations);
			$this->_view->set_param('locales', $locales);
			// $this->_view->add_script('lib/Installation/install');
		}
		
		return $this->_view;
	
	}

	private function move_files () {

		$parent = dirname(dirname(__FILE__));
		$parent_name = basename($parent);
		$project = dirname($parent);
		
		try {
			/*if (!is_dir($project . '/views')) {
				recurse_copy("$parent/Installation/app_views", $project . '/views');
			}*/
			if (!is_dir($project . '/views')) {
				mkdir("../views");
				chmod("../views", 0777);
			}
			
			if (!is_dir($project . '/controllers')) {
				mkdir("../controllers");
				chmod("../controllers", 0777);
			}
			
			if (!is_dir($project . '/modules')) {
				mkdir("../modules");
				chmod("../modules", 0777);
			}
			
			if (!is_dir($project . '/resources')) {
				mkdir($project . '/resources');
				mkdir($project . '/resources/languages');
				mkdir($project . '/resources/public');
				mkdir($project . '/resources/public/assets');
				mkdir($project . '/resources/public/css');
				mkdir($project . '/resources/public/scripts');
				chmod($project . '/resources', 0777);
				chmod($project . '/resources/languages', 0777);
				chmod($project . '/resources/public', 0777);
				chmod($project . '/resources/public/assets', 0777);
				chmod($project . '/resources/public/css', 0777);
				chmod($project . '/resources/public/scripts', 0777);
			}
			
			if (!file_exists($project . '/composer.phar')) {
				$composer = fopen($project . '/composer.phar', 'x+');
				$content = file_get_contents($parent . '/Installation/composer.phar');
				$result = fwrite($composer, $content);
				fclose($composer);
					
				if ($result === false) {
					throw new Exception('Cannot install composer');
				}
				
				chmod($project . '/composer.phar', 0777);
			}
			
			if (!file_exists($project . '/composer.json')) {
				$composer = fopen($project . '/composer.json', 'x+');
				$content = file_get_contents($parent . '/Installation/composer.json');
				$result = fwrite($composer, $content);
				fclose($composer);
					
				if ($result === false) {
					throw new Exception('Cannot create composer config');
				}
				
				chmod($project . '/composer.json', 0777);
			}
			
			if (!file_exists($project . '/index.php')) {
				//$result = copy($parent . '/Installation/empty_index.php', $project . '/index.php');
				$file_content = file_get_contents($parent . '/Installation/empty_index.php');
				$content = str_replace('{apine}', $parent_name, $file_content);
				$index = fopen($project . '/index.php', 'x+');
				$resutl = fwrite($index, $content);
				fclose($index);
				
				if ($result === false) {
					throw new Exception('Cannot move index script');
				}
				
				chmod($project . '/index.php', 0777);
			}
			
			// Compute if in a sub directory
			if (strlen($_SERVER['SCRIPT_NAME']) < 10) {
				// Remove "/index.php" from the script name
				$webroot = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
			} else {
				$webroot = '';
			}
			
			// Load .htaccess templace
			ob_start();
			include_once $parent . '/Installation/htaccess_template.php';
			$content = ob_get_contents();
			ob_end_clean();
					
			// Create htaccess
			$file = fopen($project . '/.htaccess', 'w+');
			fwrite($file, $content);
			fclose($file);
			chmod($project . '/.htaccess', 0777);
		} catch (\Exception $e) {
			throw new GenericException($e->getMessage(), $e->getCode(), $e);
		}
	
	}

	public function apply_new_config ($params) {

		try {
			if (Request::is_ajax()) {
				$entries = json_decode(Request::get_request_body(), true);
				$this->generate_config($entries);
				
				$this->import_database($entries['database']);
				$this->import_routes();
				$this->move_files();
				$this->generate_locale($entries ['localization'] ['locale_default'], $entries ['application'] ['title'], $entries ['application'] ['author'], $entries ['application'] ['description']);
			} else {
				throw new GenericException('Invalid Request', 400);
			}
		} catch (\Exception $e) {
			// throw new GenericException($e->getMessage(), $e->getCode(), $e);
			$protocol = (isset(Request::server() ['SERVER_PROTOCOL']) ? Request::server() ['SERVER_PROTOCOL'] : 'HTTP/1.0');
			header($protocol . ' 500 Internal Server Error');
		}
	
	}

	public function test_database ($params) {

		try {
			if (Request::is_ajax()) {
				$body = json_decode(Request::get_request_body());
				$database = new Database($body->type, $body->host, $body->name, $body->user, $body->pass, $body->char);
			} else {
				throw new GenericException('Invalid Request', 400);
			}
		} catch (DatabaseException $e) {
			// throw new \Apine\Exception\GenericException('Unable to connect to database', 404);
			$protocol = (isset(Request::server() ['SERVER_PROTOCOL']) ? Request::server() ['SERVER_PROTOCOL'] : 'HTTP/1.0');
			header($protocol . ' 404 Not Found');
			// print "Error";
		}
	
	}

	private function generate_config ($entries) {

		try {
			$parent = dirname(dirname(__FILE__));
			$parent_name = basename($parent);
			$project = dirname($parent);
			
			if (file_exists($project . '/config.ini')) {
				return;
			}
			
			// Add runtime section
			$entries['runtime']['token_lifespan'] = 600;
			$entries['runtime']['default_layout'] = 'default';
			
			// Write as ini config
			$result = write_ini_file($entries, $project . '/config.ini', true);
			
			if ($result === false) {
				throw new Exception('Cannot write config file');
			}
			
			chmod($project . '/config.ini', 0777);
		} catch (\Exception $e) {
			throw new GenericException($e->getMessage(), $e->getCode(), $e);
		}
	
	}

	private function generate_locale ($locale_code, $app_name, $app_author, $app_description) {

		try {
			$parent = dirname(dirname(__FILE__));
			$parent_name = basename($parent);
			$project = dirname($parent);
			
			include $parent . '/Installation/locales.php';
			
			$locale_name = $locales [$locale_code];
			$locale_code_short = substr($locale_code, 0, 2);
			
			$json_array = array();
			$json_array ['language'] = array(
					'code' => $locale_code,
					'code_short' => $locale_code_short,
					'name' => $locale_name 
			);
			$json_array ['locale'] = array(
					"datehour" => "%x %H:%M",
					"date" => "%x",
					"hour" => "%H:%M" 
			);
			$json_array ['application'] = array(
					'title' => $app_name,
					'author' => $app_author,
					'description' => $app_description 
			);
			
			if (file_exists($project . '/resources/languages/' . $locale_code . '.json')) {
				return;
			}
			
			$locale_file = fopen($project . '/resources/languages/' . $locale_code . '.json', 'x+');
			$result = fwrite($locale_file, json_encode($json_array, JSON_UNESCAPED_UNICODE));
			fclose($locale_file);
			
			if ($result === false) {
				throw new Exception('Cannot write new locale file');
			}
			
			chmod($project . '/resources/languages/' . $locale_code . '.json', 0777);
		} catch (\Exception $e) {
			throw new GenericException($e->getMessage(), $e->getCode(), $e);
		}
	
	}

	private function import_database ($entries) {

		try {
			$parent = dirname(dirname(__FILE__));
			$database = new Database($entries ['type'], $entries ['host'], $entries ['dbname'], $entries ['username'], $entries ['password'], $entries ['charset']);
			$sql_file = file_get_contents($parent . '/Installation/apine_sql_tables.sql');
			$result = $database->exec($sql_file);
			
			if ($result === false) {
				throw new \Exception('Cannot import database tables');
			}
		} catch (DatabaseException $e) {
			throw new GenericException($e->getMessage(), $e->getCode(), $e);
		}
	
	}

	private function import_routes () {

		try {
			$parent = dirname(dirname(__FILE__));
			$parent_name = basename($parent);
			$project = dirname($parent);
			
			if (Application::get_instance()->get_routes_type() == APINE_ROUTES_XML) {
				if (file_exists($project . '/routes.xml')) {
					return;
				}
				
				$routes = fopen($project . '/routes.xml', 'x+');
				$path = $project . '/routes.xml';
				$content = file_get_contents($parent . '/Installation/routes.xml');
			} else {
				if (file_exists($project . '/routes.json')) {
					return;
				}
				
				$routes = fopen($project . '/routes.json', 'x+');
				$path = $project . '/routes.json';
				$content = file_get_contents($parent . '/Installation/routes.json');
			}
			
			$result = fwrite($routes, $content);
			fclose($routes);
			
			if ($result === false) {
				throw new Exception('Cannot write routes file');
			}
			
			chmod($path, 0777);
		} catch (\Exception $e) {
			throw new GenericException($e->getMessage(), $e->getCode(), $e);
		}
	
	}

}