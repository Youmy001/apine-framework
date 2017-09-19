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
    
    public function index ($params) {
        
        $view = new TwigView();
        $view->set_title('Home');
        $view->set_view('home/home');
        $view->set_response_code(200);
        return $view;
        
    }
    
    public function about ($params) {
        
        var_dump($params);
        $view = new HTMLView();
        $view->set_title('About');
        $view->set_view('home' . DIRECTORY_SEPARATOR . 'about');
        
        $view->set_response_code(200);
        
        return $view;
        
    }
    
}