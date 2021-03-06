<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * Wfdownloads module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         wfdownload
 * @since           3.23
 * @author          Xoops Development Team
 * @version         svn:$id$
 */
defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

//$module_handler =& xoops_gethandler('module');
//$xoopsModule = & $module_handler->getByDirname(basename(dirname(dirname(__FILE__))));

// This must contain the name of the folder in which reside Wfdownloads
define("WFDOWNLOADS_DIRNAME", basename(dirname(dirname(__FILE__))));
define("WFDOWNLOADS_URL", XOOPS_URL . '/modules/' . WFDOWNLOADS_DIRNAME);
define("WFDOWNLOADS_IMAGES_URL", WFDOWNLOADS_URL . '/images');
define("WFDOWNLOADS_ADMIN_URL", WFDOWNLOADS_URL . '/admin');
define("WFDOWNLOADS_ROOT_PATH", XOOPS_ROOT_PATH . '/modules/' . WFDOWNLOADS_DIRNAME);

xoops_loadLanguage('common', WFDOWNLOADS_DIRNAME);

include_once WFDOWNLOADS_ROOT_PATH . '/include/functions.php';
include_once WFDOWNLOADS_ROOT_PATH . '/include/constants.php';
include_once WFDOWNLOADS_ROOT_PATH . '/class/session.php';
include_once WFDOWNLOADS_ROOT_PATH . '/class/wfdownloads.php';
include_once WFDOWNLOADS_ROOT_PATH . '/class/request.php';
include_once WFDOWNLOADS_ROOT_PATH . '/class/breadcrumb.php';

$debug = false;
$wfdownloads = WfdownloadsWfdownloads::getInstance($debug);

//This is needed or it will not work in blocks.
global $wfdownloads_isAdmin;

// Load only if module is installed
if (is_object($wfdownloads->getModule())) {
    // Find if the user is admin of the module
    $wfdownloads_isAdmin = wfdownloads_userIsAdmin();
}

