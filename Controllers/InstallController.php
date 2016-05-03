<?php

namespace Apine\Controllers\System;

use Apine\Core as Core;
use Apine\MVC as MVC;
use Apine\Core\Request;
use Apine\Core\Database;
use Apine\Exception\GenericException;
use Apine\Exception\DatabaseException;
use Apine\Application\Application;
use Apine\MVC\Controller;
use Apine\MVC\InstallView;
use Apine\MVC\HTMLView;

class InstallController extends MVC\Controller {
	
	const COMPOSER_LOCATION = '/vendor/youmy001/apine-framework';
	
	private $parent;
	
	private $parent_name;
	
	private $project;
	
	public function __construct() {
		
		$this->parent = dirname(dirname(__FILE__));
		$this->parent_name = basename($this->parent);
		
		if (strstr($this->parent, self::COMPOSER_LOCATION)) {
			$this->project = str_replace(self::COMPOSER_LOCATION, '', $this->parent);
		} else {
			$this->project = dirname($this->parent);
		}
		
	}

	/**
	 *
	 * @return MVC\HTMLView
	 */
	public function index () {

		if (file_exists($this->project . '/.htaccess') && file_exists($this->project . '/config.ini')) {
			apine_internal_redirect('/home');
		}
		
		if (!file_exists($this->project . '/.htaccess')) {
			$htaccess_parent = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->parent);
			// Load .htaccess templace
			ob_start();
			include_once $this->parent . '/Installation/htaccess_install.php';
			$content = ob_get_contents();
			ob_end_clean();
			
			// Create htaccess
			$file = fopen($this->project . '/.htaccess', 'x+');
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
						if (!isset($locations[$zone[0]])) {
							$locations[$zone[0]] = array("name" => $zone[0], 'zones' => array());
						}
						
						$locations[$zone[0]]['zones'][$zone [0] . '/' . $zone [1]] = array("code" => $zone [0] . '/' . $zone [1], "name" => str_replace('_', ' ', $zone [1]));
						
					}
				}
			}
			
			include $this->parent . '/Installation/locales.php';
			
			if (!class_exists('\TinyTemplate\Engine')) {
				$this->_view = new InstallView("Install APIne Framework", $this->parent . '/Views/install_view.html', $this->parent . '/Views/install_layout.html');
			} else {
				$this->_view = new HTMLView();
				$this->_view->set_title("Install APIne Framework");
				$this->_view->set_layout($this->parent . '/Views/install_layout');
				$this->_view->set_view($this->parent . '/Views/install_view');
			}
			$this->_view->set_param('timezones', $locations);
			$this->_view->set_param('locales', $locales);
			// $this->_view->add_script('lib/Installation/install');
		}
		
		return $this->_view;
	
	}

	private function move_files ($structure = true) {

		try {
			if ($structure) {
				if (!is_dir($this->project . '/views')) {
					if (!@mkdir($this->project . '/views')) {
						throw new Exception ('Cannot create directory ' . $this->project . '/views');
					}
					
					@chmod($this->project . '/views', 0777);
				}
				
				if (!is_dir($this->project . '/controllers')) {
					if (!@mkdir($this->project . '/controllers')) {
						throw new Exception ('Cannot create directory ' . $this->project . '/controllers');
					}
					
					@chmod($this->project . '/controllers', 0777);
				}
				
				if (!is_dir($this->project . '/modules')) {
					if (!@mkdir($this->project . '/modules')) {
						throw new Exception ('Cannot create directory ' . $this->project . '/modules');
					}
					
					@chmod($this->project . '/modules', 0777);
				}
				
				if (!is_dir($this->project . '/resources')) {
					if (!@mkdir($this->project . '/resources')) {
						throw new Exception ('Cannot create directory ' . $this->project . '/resources');
					}
					
					@chmod($this->project . '/resources', 0777);
				}
				
				if (!is_dir($this->project . '/resources/languages')) {
					if (!@mkdir($this->project . '/resources/languages')) {
						throw new Exception ('Cannot create directory ' . $this->project . '/resources/languages');
					}
					
					@chmod($this->project . '/resources/languages', 0777);
				}
				
				if (!is_dir($this->project . '/resources/public')) {
					if (!@mkdir($this->project . '/resources/public')) {
						throw new Exception ('Cannot create directory ' . $this->project . '/resources/public');
					}
					
					@mkdir($this->project . '/resources/public/assets');
					@mkdir($this->project . '/resources/public/css');
					@mkdir($this->project . '/resources/public/scripts');
					@chmod($this->project . '/resources/public', 0777);
					@chmod($this->project . '/resources/public/assets', 0777);
					@chmod($this->project . '/resources/public/css', 0777);
					@chmod($this->project . '/resources/public/scripts', 0777);
				}
				
				if (!file_exists($this->project . '/composer.phar') && !strstr($this->parent, self::COMPOSER_LOCATION)) {
					$composer_versions = json_decode(file_get_contents("http://getcomposer.org/versions"), true);
					$composer_stable_url = $composer_versions['stable'][0]['path'];
					$composer_phar = file_get_contents("http://getcomposer.org$composer_stable_url");
					
					$composer = fopen($this->project . '/composer.phar', 'x+');
					$result = fwrite($composer, $composer_phar);
					fclose($composer);
					
					if ($result === false) {
						throw new Exception('Cannot install composer');
					}
				
					chmod($this->project . '/composer.phar', 0777);
				}
			}
			
			if (!file_exists($this->project . '/composer.json')) {
				$composer = fopen($this->project . '/composer.json', 'x+');
				$content = file_get_contents($this->parent . '/Installation/composer.json');
				$result = fwrite($composer, $content);
				fclose($composer);
					
				if ($result === false) {
					throw new Exception('Cannot create composer config');
				}
				
				chmod($this->project . '/composer.json', 0777);
			}
			
			$htaccess_parent = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->parent);
			
			if (!file_exists($this->project . '/index.php')) {
				if (strstr($this->parent, self::COMPOSER_LOCATION)) {
					$content = file_get_contents($this->parent . '/Installation/composer_empty_index.php');
				} else {
					$file_content = file_get_contents($this->parent . '/Installation/empty_index.php');
					$content = str_replace('{apine}', $htaccess_parent, $file_content);
				}
				$index = fopen($this->project . '/index.php', 'x+');
				$result = fwrite($index, $content);
				fclose($index);
				
				if ($result === false) {
					throw new Exception('Cannot move index script');
				}
				
				chmod($this->project . '/index.php', 0777);
			}
			
			/*// Compute if in a sub directory
			if (strlen($_SERVER['SCRIPT_NAME']) > 10) {
				// Remove "/index.php" from the script name
				$webroot = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
			} else {
				$webroot = '';
			}*/
			
			// Load .htaccess templace
			ob_start();
			include_once $this->parent . '/Installation/htaccess_template.php';
			$content = ob_get_contents();
			ob_end_clean();
					
			// Create htaccess
			$file = fopen($this->project . '/.htaccess', 'w+');
			fwrite($file, $content);
			fclose($file);
			chmod($this->project . '/.htaccess', 0777);
		} catch (\Exception $e) {
			throw new GenericException($e->getMessage(), $e->getCode(), $e);
		}
	
	}

	public function apply_new_config ($params) {

		try {
			if (Request::is_ajax()) {
				$entries = json_decode(Request::get_request_body(), true);
				
				$generate = $entries['generate'];
				array_pop($entries);	// Remove the generate command from the end of the array
				
				$this->generate_config($entries);
				
				$this->import_database($entries['database']);
				$this->import_routes();
				$this->move_files($generate);
				
				if ($generate) {
					$this->generate_locale($entries ['localization'] ['locale_default'], $entries ['application'] ['title'], $entries ['application'] ['author'], $entries ['application'] ['description']);
				}
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
			$this->parent = dirname(dirname(__FILE__));
			$this->parent_name = basename($this->parent);
			
			if (strstr($this->parent, self::COMPOSER_LOCATION)) {
				$this->project = str_replace(self::COMPOSER_LOCATION, '', $this->parent);
			} else {
				$this->project = dirname($this->parent);
			}
			
			if (file_exists($this->project . '/config.ini')) {
				return;
			}
			
			// Add runtime section
			$entries['runtime']['token_lifespan'] = 600;
			$entries['runtime']['default_layout'] = 'layout';
			
			// Write as ini config
			$result = write_ini_file($entries, $this->project . '/config.ini', true);
			
			if ($result === false) {
				throw new Exception('Cannot write config file');
			}
			
			chmod($this->project . '/config.ini', 0777);
		} catch (\Exception $e) {
			throw new GenericException($e->getMessage(), $e->getCode(), $e);
		}
	
	}

	private function generate_locale ($locale_code, $app_name, $app_author, $app_description) {

		try {
			$this->parent = dirname(dirname(__FILE__));
			$this->parent_name = basename($this->parent);
			
			if (strstr($this->parent, self::COMPOSER_LOCATION)) {
				$this->project = str_replace(self::COMPOSER_LOCATION, '', $this->parent);
			} else {
				$this->project = dirname($this->parent);
			}
			
			include $this->parent . '/Installation/locales.php';
			
			$locale_name = $locales[$locale_code]['name'];
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
			
			if (file_exists($this->project . '/resources/languages/' . $locale_code . '.json')) {
				return;
			}
			
			$locale_file = fopen($this->project . '/resources/languages/' . $locale_code . '.json', 'x+');
			$result = fwrite($locale_file, json_encode($json_array, JSON_UNESCAPED_UNICODE));
			fclose($locale_file);
			
			if ($result === false) {
				throw new Exception('Cannot write new locale file');
			}
			
			chmod($this->project . '/resources/languages/' . $locale_code . '.json', 0777);
		} catch (\Exception $e) {
			throw new GenericException($e->getMessage(), $e->getCode(), $e);
		}
	
	}

	private function import_database ($entries) {

		try {
			$this->parent = dirname(dirname(__FILE__));
			$database = new Database($entries ['type'], $entries ['host'], $entries ['dbname'], $entries ['username'], $entries ['password'], $entries ['charset']);
			$sql_file = file_get_contents($this->parent . '/Installation/apine_sql_tables.sql');
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
			$this->parent = dirname(dirname(__FILE__));
			$this->parent_name = basename($this->parent);
			
			if (strstr($this->parent, self::COMPOSER_LOCATION)) {
				$this->project = str_replace(self::COMPOSER_LOCATION, '', $this->parent);
			} else {
				$this->project = dirname($this->parent);
			}
			
			if (Application::get_instance()->get_routes_type() == APINE_ROUTES_XML) {
				if (file_exists($this->project . '/routes.xml')) {
					return;
				}
				
				$routes = fopen($this->project . '/routes.xml', 'x+');
				$path = $this->project . '/routes.xml';
				$content = file_get_contents($this->parent . '/Installation/routes.xml');
			} else {
				if (file_exists($this->project . '/routes.json')) {
					return;
				}
				
				$routes = fopen($this->project . '/routes.json', 'x+');
				$path = $this->project . '/routes.json';
				$content = file_get_contents($this->parent . '/Installation/routes.json');
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