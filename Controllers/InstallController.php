<?php
/**
 * Installation Controller
 *
 * @license MIT
 * @copyright 2016 Tommy Teasdale
 */
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
use \Exception;

/**
 * Class InstallController
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.comm>
 * @package Apine\Controllers\System
 */
class InstallController extends MVC\Controller {

    /**
     * Location of the framework in Composer
     */
	const COMPOSER_LOCATION = '/vendor/youmy001/apine-framework';

    /**
     * @var string Parent directory
     */
	private $parent;

    /**
     * @var string Name of the parent directory
     */
	private $parent_name;

    /**
     * @var string Project directory
     */
	private $project;

    /**
     * @var array Locales available
     */
    private $locales = array(
        'ar_AE' => array('code'=> 'ar_AE', 'name' => 'العربية (الإمارات العربية المتحدة)'),
        'ar_EG' => array('code'=> 'ar_EG', 'name' => 'العربية (مصر)'),
        'ar_IQ' => array('code'=> 'ar_IQ', 'name' => 'العربية (العراق)'),
        'ar_MA' => array('code'=> 'ar_MA', 'name' => 'العربية (المغرب)'),
        'de_DE' => array('code'=> 'de_DE', 'name' => 'Deutsche (Deutschland)'),
        'de_AT' => array('code'=> 'de_AT', 'name' => 'Deutsche (Österreich)'),
        'el_EL' => array('code'=> 'el_EL', 'name' => 'Ελληνικά'),
        'en_US' => array('code'=> 'en_US', 'name' => 'English (US)'),
        'en_GB' => array('code'=> 'en_GB', 'name' => 'English (UK)'),
        'es_ES' => array('code'=> 'es_ES', 'name' => 'Español'),
        'fi_FI' => array('code'=> 'fi_FI', 'name' => 'Suomi'),
        'fr_FR' => array('code'=> 'fr_FR', 'name' => 'Français (France)'),
        'fr_CA' => array('code'=> 'fr_CA', 'name' => 'Français (Canada)'),
        'he_IL' => array('code'=> 'he_IL', 'name' => 'עִברִית'),
        'it_IT' => array('code'=> 'it_IT', 'name' => 'Italiano'),
        'nl_NL' => array('code'=> 'nl_NL', 'name' => 'Nederlands'),
        'pt_BR' => array('code'=> 'pt_BR', 'name' => 'Português (Brasil)'),
        'pt_PT' => array('code'=> 'pt_PT', 'name' => 'Português (Portugal)'),
        'ru_RU' => array('code'=> 'ru_RU', 'name' => 'Русский'),
        'th_TH' => array('code'=> 'th_TH', 'name' => 'ภาษาไทย'),
        'vi_VN' => array('code'=> 'vi_VN', 'name' => 'Tiếng Việt'),
        'cy_GB' => array('code'=> 'cy_GB', 'name' => 'Cymraeg'),
        'kr_KR' => array('code'=> 'kr_KR', 'name' => '한국'),
        'ja_JP' => array('code'=> 'ja_JP', 'name' => '日本語'),
        'zh_CN' => array('code'=> 'zh_CN', 'name' => '中文 (简体)'),
        'zh_TW' => array('code'=> 'zh_TW', 'name' => '中文 (繁体)')

    );

    /**
     * InstallController constructor.
     */
	public function __construct() {
		
		parent::__construct();
		
		$this->parent = str_replace(DIRECTORY_SEPARATOR, '/', dirname(dirname(__FILE__)));
		$this->parent_name = basename($this->parent);
		
		if (strstr($this->parent, self::COMPOSER_LOCATION)) {
			$this->project = str_replace(self::COMPOSER_LOCATION, '', $this->parent);
		} else {
			$this->project = dirname($this->parent);
		}

		require_once $this->parent . '/Includes/Functions.php';
		
	}

	/**
	 * Default Action
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
            $locations = array();
			
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
			
			//include $this->parent . '/Installation/locales.php';
			
			if (!class_exists('\TinyTemplate\Engine')) {
				$this->_view = new InstallView("Install APIne Framework", $this->parent . '/Views/install_view.html', $this->parent . '/Views/install_layout.html');
			} else {
				$this->_view = new HTMLView();
				$this->_view->set_title("Install APIne Framework");
				$this->_view->set_layout($this->parent . '/Views/install_layout');
				$this->_view->set_view($this->parent . '/Views/install_view');
			}
			$this->_view->set_param('timezones', $locations);
			$this->_view->set_param('locales', $this->locales);
		}
		
		return $this->_view;
	
	}

    /**
     * Move basic assets
     *
     * @param bool $structure Move assets for project structure
     * @throws GenericException
     */
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

    /**
     * Apply new configuration
     *
     * @param $params
     */
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
			$protocol = (isset(Request::server() ['SERVER_PROTOCOL']) ? Request::server() ['SERVER_PROTOCOL'] : 'HTTP/1.0');
			header($protocol . ' 500 Internal Server Error');
		}
	
	}

    /**
     * Test Database Connection
     *
     * @param array $params
     * @throws GenericException
     */
	public function test_database ($params) {

		try {
			if (Request::is_ajax()) {
				$body = json_decode(Request::get_request_body());
				$database = new Database($body->type, $body->host, $body->name, $body->user, $body->pass, $body->char);
			} else {
				throw new GenericException('Invalid Request', 400);
			}
		} catch (DatabaseException $e) {
			$protocol = (isset(Request::server() ['SERVER_PROTOCOL']) ? Request::server() ['SERVER_PROTOCOL'] : 'HTTP/1.0');
			header($protocol . ' 404 Not Found');
		}
	
	}

    /**
     * Generate the default configuration file from template and user inputs
     *
     * @param $entries
     * @throws GenericException
     */
	private function generate_config ($entries) {

		try {
			if (file_exists($this->project . '/config.ini')) {
				return;
			}
			
			// Add runtime section
			$entries['runtime']['token_lifespan'] = 600;
			$entries['runtime']['default_layout'] = 'layout';
			$entries['runtime']['encryption_method'] = "ssl";
			
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

    /**
     * Generate default locale
     *
     * @param string $locale_code
     * @param string $app_name
     * @param string $app_author
     * @param string $app_description
     * @throws GenericException
     */
	private function generate_locale ($locale_code, $app_name, $app_author, $app_description) {

		try {
			
			$locale_name = $this->locales[$locale_code]['name'];
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

    /**
     * Import APIne's table in the database
     *
     * @param array $entries
     * @throws Exception
     * @throws GenericException
     */
	private function import_database ($entries) {

		try {
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

    /**
     * Write the default routes file
     *
     * @throws GenericException
     */
	private function import_routes () {

		try {
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


