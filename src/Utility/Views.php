<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/09/25
 * Time: 14:56
 */

namespace Apine\Utility;

use Apine\Application\Application;
use Apine\Core\Config;
use Apine\Application\Translator;
use Apine\MVC\URLHelper;
use Apine\Session\SessionManager;


class Views
{
    /**
     * Return the instance of the Apine Application
     *
     * @return Application
     */
    function application()
    {
        return Application::getInstance();
    }
    
    
    /**
     * Return the instance of the Apine Config
     *
     * @return Config
     */
    function application_config()
    {
        return Application::getInstance()->getConfig();
    }
    
    
    /**
     * Return the instance of the Session Manager
     *
     * @return SessionManager
     */
    function session_manager()
    {
        return SessionManager::getInstance();
    }
    
    
    /**
     * Return the instance of the Application Translator
     *
     * @return Translator
     */
    function application_translator()
    {
        return Translator::getInstance();
    }
    
    
    /**
     * Return the instance of the URL Helper
     *
     * @return URLHelper
     */
    function url_helper()
    {
        return URLHelper::getInstance();
    }
    
}