<?php
/**
 * APIne Framework Main Execution
 * This script runs basic environment setup and launches userside code
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

ini_set('display_errors', -1);

require_once 'lib/Autoloader.php';
$loader = new Apine\Autoloader();
$loader->register();

$apine = new Apine\Application\Application();

$apine->set_mode(APINE_MODE_DEVELOPMENT);
$apine->set_use_https(true);

$apine->run(APINE_RUNTIME_HYBRID);
