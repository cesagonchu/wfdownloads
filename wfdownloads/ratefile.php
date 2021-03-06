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
include 'header.php';

$lid      = WfdownloadsRequest::getInt('lid', 0);
$download = $wfdownloads->getHandler('download')->get($lid);
if (empty($download)) {
    redirect_header('index.php', 3, _CO_WFDOWNLOADS_ERROR_NODOWNLOAD);
}
$cid      = WfdownloadsRequest::getInt('cid', $download->getVar('cid'));
$category = $wfdownloads->getHandler('category')->get($cid);
if (empty($category)) {
    redirect_header('index.php', 3, _CO_WFDOWNLOADS_ERROR_NOCATEGORY);
}

// Download not published, expired or taken offline - redirect
if ($download->getVar('published') == false || $download->getVar('published') > time() || $download->getVar('offline') == true
    || ($download->getVar(
            'expired'
        ) != 0
        && $download->getVar('expired') < time())
) {
    redirect_header("index.php", 3, _MD_WFDOWNLOADS_NODOWNLOAD);
}

// Check permissions
if ($wfdownloads->getConfig('enable_ratings') == false && !wfdownloads_userIsAdmin()) {
    redirect_header('index.php', 3, _NOPERM);
}
// Breadcrumb
include_once XOOPS_ROOT_PATH . "/class/tree.php";
$categoriesTree = new XoopsObjectTree($wfdownloads->getHandler('category')->getObjects(), 'cid', 'pid');
$breadcrumb     = new WfdownloadsBreadcrumb();
$breadcrumb->addLink($wfdownloads->getModule()->getVar('name'), WFDOWNLOADS_URL);
foreach (array_reverse($categoriesTree->getAllParent($cid)) as $parentCategory) {
    $breadcrumb->addLink($parentCategory->getVar('title'), "viewcat.php?cid=" . $parentCategory->getVar('cid'));
}
$breadcrumb->addLink($category->getVar('title'), "viewcat.php?cid={$cid}");
$breadcrumb->addLink($download->getVar('title'), "singlefile.php?lid={$lid}");

$op = WfdownloadsRequest::getString('op', 'vote.add');
switch ($op) {
    case "vote.add" :
    default :
        // Get vote poster 'uid'
        $ratinguserUid = is_object($xoopsUser) ? (int)$xoopsUser->getVar('uid') : 0;
        $ratinguserIp  = getenv("REMOTE_ADDR");

        if (!empty($_POST['submit'])) {
            $rating = WfdownloadsRequest::getString('rating', '--', 'POST');

            // Check if Rating is Null
            if ($rating == '--') {
                redirect_header("?cid={$cid}&amp;lid={$lid}", 4, _MD_WFDOWNLOADS_NORATING);
                exit();
            }
            if ($ratinguserUid != 0) {
                // Check if Download POSTER is voting (UNLESS Anonymous users allowed to post)
                if ($download->getVar('submitter') == $ratinguserUid) {
                    redirect_header(WFDOWNLOADS_URL . "/singlefile.php?cid={$cid}&amp;lid={$lid}", 4, _MD_WFDOWNLOADS_CANTVOTEOWN);
                    exit();
                }
                // Check if REG user is trying to vote twice.
                $criteria = new CriteriaCompo(new Criteria('lid', $lid));
                $criteria->add(new Criteria('ratinguser', $ratinguserUid));
                $ratingsCount = $wfdownloads->getHandler('rating')->getCount($criteria);
                if ($ratingsCount > 0) {
                    redirect_header("singlefile.php?cid={$cid}&amp;lid={$lid}", 4, _MD_WFDOWNLOADS_VOTEONCE);
                    exit();
                }
            } else {
                // Check if ANONYMOUS user is trying to vote more than once per day (only 1 anonymous from an IP in a single day).
                $anonymousWaitDays = 1;
                $yesterday         = (time() - (86400 * $anonymousWaitDays));
                $criteria          = new CriteriaCompo(new Criteria("lid", $lid));
                $criteria->add(new Criteria('ratinguser', 0));
                $criteria->add(new Criteria('ratinghostname', $ratinguserIp));
                $criteria->add(new Criteria('ratingtimestamp', $yesterday, '>'));
                $anonymousVotesCount = $wfdownloads->getHandler('rating')->getCount($criteria);
                if ($anonymousVotesCount > 0) {
                    redirect_header("singlefile.php?cid={$cid}&amp;lid={$lid}", 4, _MD_WFDOWNLOADS_VOTEONCE);
                    exit();
                }
            }
            // All is well. Add to Line Item Rate to DB.
            $rating = $wfdownloads->getHandler('rating')->create();
            $rating->setVar('lid', $lid);
            $rating->setVar('ratinguser', $ratinguserUid);
            $rating->setVar('rating', (int)$rating);
            $rating->setVar('ratinghostname', $ratinguserIp);
            $rating->setVar('ratingtimestamp', time());
            if ($wfdownloads->getHandler('rating')->insert($rating)) {
                // All is well. Calculate Score & Add to Summary (for quick retrieval & sorting) to DB.
                wfdownloads_updateRating($lid);
                $thankyouMessage = _MD_WFDOWNLOADS_VOTEAPPRE . "<br />" . sprintf(_MD_WFDOWNLOADS_THANKYOU, $xoopsConfig['sitename']);
                redirect_header("singlefile.php?cid={$cid}&amp;lid={$lid}", 4, $thankyouMessage);
            } else {
                echo $rating->getHtmlErrors();
            }
        } else {
            $xoopsOption['template_main'] = 'wfdownloads_ratefile.html';
            include XOOPS_ROOT_PATH . '/header.php';

            $xoTheme->addStylesheet(WFDOWNLOADS_URL . '/module.css');
            $xoTheme->addStylesheet(WFDOWNLOADS_URL . '/thickbox.css');
            $xoopsTpl->assign('wfdownloads_url', WFDOWNLOADS_URL . '/');

            // Breadcrumb
            $breadcrumb->addLink(_MD_WFDOWNLOADS_RATETHISFILE, '');
            $xoopsTpl->assign('wfdownloads_breadcrumb', $breadcrumb->render());

            // Generate form
            include XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
            $sform         = new XoopsThemeForm(_MD_WFDOWNLOADS_RATETHISFILE, 'voteform', xoops_getenv('PHP_SELF'));
            $rating_select = new XoopsFormSelect(_MD_WFDOWNLOADS_REV_RATING, 'rating', '10');
            //$rating_select->setDescription(_MD_WFDOWNLOADS_REV_RATING_DESC);
            $rating_select->addOptionArray(
                array(
                     '1'  => 1,
                     '2'  => 2,
                     '3'  => 3,
                     '4'  => 4,
                     '5'  => 5,
                     '6'  => 6,
                     '7'  => 7,
                     '8'  => 8,
                     '9'  => 9,
                     '10' => 10
                )
            );
            $sform->addElement($rating_select);
            $sform->addElement(new XoopsFormHidden('lid', $lid));
            $sform->addElement(new XoopsFormHidden('cid', $cid));
            $sform->addElement(new XoopsFormHidden('uid', $reviewerUid));
            $button_tray   = new XoopsFormElementTray('', '');
            $submit_button = new XoopsFormButton('', 'submit', _MD_WFDOWNLOADS_RATEIT, 'submit');
            $button_tray->addElement($submit_button);
            $cancel_button = new XoopsFormButton('', '', _CANCEL, 'button');
            $cancel_button->setExtra('onclick="history.go(-1)"');
            $button_tray->addElement($cancel_button);
            $sform->addElement($button_tray);
            $xoopsTpl->assign('voteform', $sform->render());
            $xoopsTpl->assign(
                'download',
                array('lid' => $lid, 'cid' => $cid, 'title' => $download->getVar('title'), 'description' => $download->getVar('description'))
            );

            $xoopsTpl->assign(
                'file',
                array('id' => $lid, 'lid' => $lid, 'cid' => $cid, 'title' => $download->getVar('title'), 'imageheader' => wfdownloads_headerImage())
            ); // this definition is not removed for backward compatibility issues
            include 'footer.php';
        }
        break;
}
