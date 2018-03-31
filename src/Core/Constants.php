<?php
/**
 * Constants
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core;

/**
 * #@+
 * Constants
 */
/*define('APINE_MODE_DEVELOPMENT', 5);
define('APINE_MODE_PRODUCTION', 6);
define('APINE_PROTOCOL_HTTP', 0);
define('APINE_PROTOCOL_HTTPS', 1);
define('APINE_PROTOCOL_DEFAULT', 2);
define('APINE_RUNTIME_API', 16);
define('APINE_RUNTIME_APP', 17);
define('APINE_RUNTIME_HYBRID', 18);
define('APINE_SESSION_ADMIN', 77);
define('APINE_SESSION_USER', 65);
define('APINE_SESSION_GUEST', 40);
define('APINE_SESSION_DELETED', 10);
define('APINE_REQUEST_USER', 48);
define('APINE_REQUEST_MACHINE', 96);*/

const PROTOCOL_HTTP = 0;
const PROTOCOL_HTTPS = 1;
const PROTOCOL_DEFAULT = 2;

const REQUEST_USER = 48;
const REQUEST_MACHINE = 96;