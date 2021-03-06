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
$currentFile = basename(__FILE__);
include_once dirname(__FILE__) . '/admin_header.php';

include_once dirname(dirname(__FILE__)) . '/include/directorychecker.php';
include_once dirname(dirname(__FILE__)) . '/include/filechecker.php';

xoops_cp_header();
$indexAdmin = new ModuleAdmin();

//--------------------------
$categories_count           = wfdownloads_categoriesCount();
$brokenDownloads_count      = $wfdownloads->getHandler('report')->getCount();
$modificationRequests_count = $wfdownloads->getHandler('modification')->getCount();
$newReviews_count           = $wfdownloads->getHandler('review')->getCount();
$newMirrors_count           = $wfdownloads->getHandler('mirror')->getCount();
$newDownloads_count         = $wfdownloads->getHandler('download')->getCount(new Criteria('published', 0));
$downloads_count            = $wfdownloads->getHandler('download')->getCount(new Criteria('published', 0, '>'));

$indexAdmin->addInfoBox(_AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY);
// Categories
if ($categories_count > 0) {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel><a href="categories.php">' . _AM_WFDOWNLOADS_SCATEGORY . '</a></infolabel>',
        $categories_count,
        'green'
    );
} else {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel>' . _AM_WFDOWNLOADS_SCATEGORY . '</infolabel>',
        $categories_count,
        'green'
    );
}
// Downloads
if ($downloads_count > 0) {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel><a href="downloads.php">' . _AM_WFDOWNLOADS_SFILES . '</a><b></infolabel>',
        $downloads_count,
        'green'
    );
} else {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel>' . _AM_WFDOWNLOADS_SFILES . '</infolabel>',
        $downloads_count,
        'green'
    );
}
// New/waiting downloads
if ($newDownloads_count > 0) {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel><a href="downloads.php">' . _AM_WFDOWNLOADS_SNEWFILESVAL . '</a></infolabel>',
        $newDownloads_count,
        'green'
    );
} else {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel>' . _AM_WFDOWNLOADS_SNEWFILESVAL . '</infolabel>',
        $newDownloads_count,
        'green'
    );
}
// Reviews
if ($wfdownloads->getConfig('enable_reviews') == false) {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel>' . _AM_WFDOWNLOADS_SREVIEWS . '</infolabel>',
        _CO_WFDOWNLOADS_DISABLED,
        'red'
    );
} elseif ($newReviews_count > 0) {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel><a href="reviews.php">' . _AM_WFDOWNLOADS_SREVIEWS . '</a></infolabel>',
        $newReviews_count,
        'green'
    );
} else {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel>' . _AM_WFDOWNLOADS_SREVIEWS . '</infolabel>',
        $newReviews_count,
        'green'
    );
}
// Modifications
if ($modificationRequests_count > 0) {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel><a href="reportsmodifications.php">' . _AM_WFDOWNLOADS_SMODREQUEST . '</a></infolabel>',
        $modificationRequests_count,
        'green'
    );
} else {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel>' . _AM_WFDOWNLOADS_SMODREQUEST . '</infolabel>',
        $modificationRequests_count,
        'green'
    );
}
// Brokens reports
if ($wfdownloads->getConfig('enable_brokenreports') == false) {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel>' . _AM_WFDOWNLOADS_SBROKENSUBMIT . '</infolabel>',
        _CO_WFDOWNLOADS_DISABLED,
        'red'
    );
} elseif ($brokenDownloads_count > 0) {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel><a href="reportsmodifications.php">' . _AM_WFDOWNLOADS_SBROKENSUBMIT . '</a></infolabel>',
        $brokenDownloads_count,
        'green'
    );
} else {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel>' . _AM_WFDOWNLOADS_SBROKENSUBMIT . '</infolabel>',
        $brokenDownloads_count,
        'green'
    );
}
// Mirrors
if ($wfdownloads->getConfig('enable_mirrors') == false) {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel>' . _AM_WFDOWNLOADS_SMIRRORS . '</infolabel>',
        _CO_WFDOWNLOADS_DISABLED,
        'red'
    );
} elseif ($newMirrors_count > 0) {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel><a href="mirrors.php">' . _AM_WFDOWNLOADS_SMIRRORS . '</a></infolabel>',
        $newMirrors_count,
        'green'
    );
} else {
    $indexAdmin->addInfoBoxLine(
        _AM_WFDOWNLOADS_MINDEX_DOWNSUMMARY,
        '<infolabel>' . _AM_WFDOWNLOADS_SMIRRORS . '</infolabel>',
        $newMirrors_count,
        'green'
    );
}

//------ check directories ---------------

$indexAdmin->addConfigBoxLine('');
$redirectFile = $_SERVER['PHP_SELF'];

if (!wfdownloads_checkModule('formulize')) {
    $indexAdmin->addConfigBoxLine(_AM_WFDOWNLOADS_FORMULIZE_NOT_AVILABLE);
} else {
    $indexAdmin->addConfigBoxLine(_AM_WFDOWNLOADS_FORMULIZE_AVAILABLE);
}

$indexAdmin->addConfigBoxLine('');

$path = $wfdownloads->getConfig('uploaddir') . '/';
$indexAdmin->addConfigBoxLine(DirectoryChecker::getDirectoryStatus($path, 0777, $redirectFile));

$indexAdmin->addConfigBoxLine('');

$path = XOOPS_ROOT_PATH . '/' . $wfdownloads->getConfig('mainimagedir') . '/';
$indexAdmin->addConfigBoxLine(DirectoryChecker::getDirectoryStatus($path, 0777, $redirectFile));
$indexAdmin->addConfigBoxLine(FileChecker::getFileStatus($path . 'blank.gif', dirname(dirname(__FILE__)) . '/images/blank.gif', $redirectFile));

$indexAdmin->addConfigBoxLine('');

$path = XOOPS_ROOT_PATH . '/' . $wfdownloads->getConfig('screenshots') . '/';
$indexAdmin->addConfigBoxLine(DirectoryChecker::getDirectoryStatus($path, 0777, $redirectFile));
$indexAdmin->addConfigBoxLine(FileChecker::getFileStatus($path . 'blank.gif', dirname(dirname(__FILE__)) . '/images/blank.gif', $redirectFile));
$indexAdmin->addConfigBoxLine(DirectoryChecker::getDirectoryStatus($path . 'thumbs' . '/', 0777, $redirectFile));
$indexAdmin->addConfigBoxLine(
    FileChecker::getFileStatus($path . 'thumbs' . '/' . 'blank.gif', dirname(dirname(__FILE__)) . '/images/blank.gif', $redirectFile)
);

$indexAdmin->addConfigBoxLine('');

$path = XOOPS_ROOT_PATH . '/' . $wfdownloads->getConfig('catimage') . '/';
$indexAdmin->addConfigBoxLine(DirectoryChecker::getDirectoryStatus($path, 0777, $redirectFile));
$indexAdmin->addConfigBoxLine(FileChecker::getFileStatus($path . 'blank.gif', dirname(dirname(__FILE__)) . '/images/blank.gif', $redirectFile));
$indexAdmin->addConfigBoxLine(DirectoryChecker::getDirectoryStatus($path . 'thumbs' . '/', 0777, $redirectFile));
$indexAdmin->addConfigBoxLine(
    FileChecker::getFileStatus($path . 'thumbs' . '/' . 'blank.gif', dirname(dirname(__FILE__)) . '/images/blank.gif', $redirectFile)
);

//---------------------------

echo $indexAdmin->addNavigation('index.php');
echo $indexAdmin->renderIndex();
echo wfdownloads_serverStats();

include 'admin_footer.php';
