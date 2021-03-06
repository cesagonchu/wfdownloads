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

if (!defined("XOOPS_ROOT_PATH")) {
     die("XOOPS root path not defined");
}
include_once dirname(__FILE__) . '/common.php';

// comment callback functions

function wfdownloads_com_update($download_id, $total_num)
{
    $wfdownloads = WfdownloadsWfdownloads::getInstance();
    $wfdownloads->getHandler('download')->updateAll("comments", intval($total_num), new Criteria("lid", intval($download_id)));
}

function wfdownloads_com_approve(&$comment)
{
    // notification mail here
}
