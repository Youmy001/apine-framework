<?php

/**
 * A split method that supports unicode characters
 *
 * @param string  $str
 * @param integer $l
 *
 * @return string
 * @see \Apine\Utility\StringManipulation::splitUnicode()
 */
function str_split_unicode($str, $l = 0)
{
    
    return \Apine\Utility\StringManipulation::splitUnicode($str, $l);
    
}

/**
 * Check if a string is a valid ISO 8601 Timestamp
 *
 * @param string $timestamp
 *
 * @return boolean
 * @see \Apine\Utility\Types::isTimestamp()
 */
function is_timestamp($timestamp)
{
    
    return \Apine\Utility\Types::isTimestamp($timestamp);
    
}


/**
 * Check if a string is a valid JSON string
 *
 * @param string $string
 *
 * @return boolean
 * @see \Apine\Utility\Types::isJson()
 */
function is_json($string)
{
    
    return \Apine\Utility\Types::isJson($string);
    
}

/**
 * Loads all files recursively a user defined module in the model/
 * directory
 *
 * @param string $module_name
 *        Name of the folder of the module
 *
 * @return boolean
 */
function apine_load_module($module_name)
{
    
    return Apine\Autoloader::loadModule($module_name);
    
}

/**
 * Write strings in a configuration file in INI format
 *
 * @param array   $assoc_arr
 * @param string  $path
 * @param boolean $has_sections
 *
 * @return boolean
 * @see \Apine\Utility\Files::writeIniFile()
 */
function write_ini_file($assoc_arr, $path, $has_sections = false)
{
    
    return \Apine\Utility\Files::writeIniFile($assoc_arr, $path, $has_sections);
    
}

/**
 * Compute a ratio from a multiplier
 *
 * @param double $n
 *        Ratio multiplier
 * @param float  $tolerance
 *        Precision level of the procedure
 *
 * @return string
 * @see \Apine\Utility\Math::floatToRatio()
 */
function float2rat($n, $tolerance = 1.e-6)
{
    
    return \Apine\Utility\Math::floatToRatio($n, $tolerance);
    
}

/**
 * Verify if exec is disabled
 *
 * @see \Apine\Utility\Files::isExecAvailable()
 */
function is_exec_available()
{
    return \Apine\Utility\Files::isExecAvailable();
}

/**
 * Recursive file copy
 *
 * @param string $src
 *            Source directory
 * @param string $dst
 *            Destination directory
 *
 * @see \Apine\Utility\Files::recursiveCopy()
 */
function recurse_copy($src, $dst)
{
    \Apine\Utility\Files::recursiveCopy($src, $dst);
}

/**
 * Redirect to another end point of the application
 * using a full query string
 *
 * @param string  $a_request
 * @param integer $a_protocol
 *
 * @return Apine\MVC\RedirectionView
 * @see \Apine\Utility\Routes::internalRedirect()
 */
function apine_internal_redirect($a_request, $a_protocol = APINE_PROTOCOL_DEFAULT)
{
    return \Apine\Utility\Routes::internalRedirect($a_request, $a_protocol);
}


/**
 * Safely redirect to another URI.
 *
 * @param string $a_request
 *
 * @return Apine\MVC\RedirectionView
 * @see \Apine\Utility\Routes::redirect()
 */
function apine_redirect($a_request)
{
    return \Apine\Utility\Routes::redirect($a_request);
}


/**
 * Return the instance of the Apine Application
 *
 * @return Apine\Application\Application
 */
function apine_application()
{
    return Apine\Application\Application::getInstance();
}


/**
 * Return the instance of the Apine Config
 *
 * @return Apine\Core\Config
 */
function apine_app_config()
{
    return Apine\Application\Application::getInstance()->getConfig();
}


/**
 * Return the instance of the Session Manager
 *
 * @return Apine\Session\SessionManager
 */
function apine_session()
{
    return Apine\Session\SessionManager::get_instance();
}


/**
 * Return the instance of the Application Translator
 *
 * @return Apine\Application\Translator
 */
function apine_app_translator()
{
    return Apine\Application\Translator::getInstance();
}


/**
 * Return the instance of the URL Helper
 *
 * @return Apine\MVC\URLHelper
 */
function apine_url_helper()
{
    return Apine\MVC\URLHelper::get_instance();
}


/**
 * Return the extension from a file name
 *
 * @param string $a_file_path
 *
 * @return string
 * @see \Apine\Utility\Files::fileExtension()
 */
function file_extension($a_file_path)
{
    return \Apine\Utility\Files::fileExtension($a_file_path);
}

/**
 * @param mixed   $var
 * @param string  $function
 * @param boolean $negate
 *
 * @return boolean
 * @see \Apine\Utility\Types::isReference()
 */
function is_reference(&$var, $function = '', $negate = false)
{
    return \Apine\Utility\Types::isReference($var, $function, $negate);
}

/**
 * @return string|null Calling Class
 */
function get_calling_class()
{
    
    return \Apine\Utility\Types::getCallingClass();
    
}

/**
 * Export XML routes in a JSON Format
 *
 * @param string $file
 *
 * @return array
 */
function apine_export_routes($file)
{
    
    return \Apine\Utility\Routes::exportToJson($file);
    
}