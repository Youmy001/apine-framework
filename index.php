<?php
/**
 * APIne Framework Main Execution
 * This script runs basic environment setup and launches userside code
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

//$before = microtime(true) * 1000;
ini_set('display_errors', -1);

require_once('lib/autoloader.php');
ApineAutoload::load_kernel();

$apine = new ApineApplication();

$apine->set_mode(APINE_MODE_DEVELOPMENT);
$apine->set_use_https(true);

$apine->run(APINE_RUNTIME_APP);
