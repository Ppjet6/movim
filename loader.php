<?php

// A few constants...
define('BASE_PATH', dirname(__FILE__) . '/');
define('APP_NAME', 'movim');
define('LIB_PATH', BASE_PATH.'system/');
define('PROPERTIES_PATH', BASE_PATH.'page/properties/');
define('THEMES_PATH', BASE_PATH . 'themes/');
define('USERS_PATH', BASE_PATH . 'users/');

//define('DB_DEBUG', true);
//define('DB_LOGFILE', BASE_PATH . 'log/queries.log');

// Loads up all system libraries.
require_once(LIB_PATH . "Lang/i18n.php");


require_once(LIB_PATH . "Session.php");
require_once(LIB_PATH . "Utils.php");
require_once(LIB_PATH . "UtilsString.php");
require_once(LIB_PATH . "UtilsPicture.php");
require_once(LIB_PATH . "Cache.php");
require_once(LIB_PATH . "Conf.php");
require_once(LIB_PATH . "Event.php");
require_once(LIB_PATH . "Logger.php");
require_once(LIB_PATH . "MovimException.php");
require_once(LIB_PATH . "RPC.php");
require_once(LIB_PATH . "User.php");
require_once(LIB_PATH . "XMPPtoForm.php");

// Markdown lib
require_once(LIB_PATH . "Markdown.php");

// We load Movim Data Layer
require_once(LIB_PATH . 'Modl/loader.php');

$db = modl\Modl::getInstance();
$db->setConnection(Conf::getServerConfElement('db'));

// We load Movim XMPP Library
require_once(LIB_PATH . 'Moxl/loader.php');

require_once(LIB_PATH . "Controller/ControllerBase.php");
require_once(LIB_PATH . "Controller/ControllerMain.php");
require_once(LIB_PATH . "Controller/ControllerAjax.php");

require_once(LIB_PATH . "Tpl/TplPageBuilder.php");

require_once(LIB_PATH . "Widget/WidgetBase.php");
require_once(LIB_PATH . "Widget/WidgetCommon.php");
require_once(LIB_PATH . "Widget/WidgetWrapper.php");

// We set the default timezone to the server timezone

date_default_timezone_set(getLocalTimezone());

$useragent = $_SERVER['HTTP_USER_AGENT'];

if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$useragent,$matched)) {
    $browser_version=$matched[1];
    $browser = 'IE';
} elseif (preg_match('/Opera[\/ ]([0-9]{1}\.[0-9]{1}([0-9])?)/',$useragent,$matched)) {
    $browser_version=$matched[1];
    $browser = 'Opera';
} elseif(preg_match('|Firefox/([0-9\.]+)|',$useragent,$matched)) {
    $browser_version=$matched[1];
    $browser = 'Firefox';
} elseif(preg_match('|Safari/([0-9\.]+)|',$useragent,$matched)) {
    $browser_version=$matched[1];
    $browser = 'Safari';
} else {
    $browser_version = 0;
    $browser= 'other';
}

define('BROWSER_VERSION', $browser_version);
define('BROWSER', $browser);

$compatible = false;

switch($browser) {
    case 'Firefox':
        if($browser_version > 3.5)
            $compatible = true;
    break;
    case 'IE':
        if($browser_version > 8.0)
            $compatible = true;
    break;
    case 'Safari': // Also Chrome-Chromium
        if($browser_version > 522.0)
            $compatible = true;
    break;
    case 'Opera':
        if($browser_version > 9.0)
            $compatible = true;
    break;
}

define('BROWSER_COMP', $compatible);

// Starting session.
$sess = Session::start(APP_NAME);
$session = $sess->get('session');
?>
