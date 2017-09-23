<?php
/**
 * Home Controller
 *
 * @license MIT
 * @copyright 2015-2017 Tommy Teasdale
 */
namespace Apine\Controllers\User;

use Apine\Application\Application;
use Apine\Application\Translator;
use Apine\Core\Config;
use Apine\Core\Database;
use Apine\Entity\BasicEntity;
use Apine\MVC\HTMLView;
use Apine\MVC\TwigView;
use Apine\MVC;
use Apine\Session as Session;
use Apine\Core\Encryption;
use Apine\User\User;
use Apine\User\UserGroup;
use Apine\User\Factory\UserGroupFactory;
use Apine\Core\Collection;

class HomeController extends MVC\Controller {
	
	public function index () {

		$view = new TwigView();
		$view->set_title('Home');
		$view->set_view('home/home');
		$view->set_response_code(200);
		
		if (Session\SessionManager::is_logged_in()) {
			$user = Session\SessionManager::get_user();
			$number_access = $user->get_property('access_nb');
			$user->set_property('access_nb', $number_access+1);
			$user->save();
		}
		
		return $view;
		
	}
	
	public function about () {
		
		$view = new TwigView();
		$view->set_title('About');
		$view->set_view('home/about');
	
		$view->set_response_code(200);
		
		return $view;
		
	}
	
}