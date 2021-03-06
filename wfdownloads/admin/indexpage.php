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

$op = WfdownloadsRequest::getString('op', 'indexpage.form');
switch ($op) {
    case "indexpage.save":
        $indexheading     = $myts->addslashes($_POST['indexheading']);
        $indexheader      = $myts->addslashes($_POST['indexheader']);
        $indexfooter      = $myts->addslashes($_POST['indexfooter']);
        $indeximage       = $myts->addslashes($_POST['indeximage']);
        $nohtml           = isset($_POST['nohtml']);
        $nosmiley         = isset($_POST['nosmiley']);
        $noxcodes         = isset($_POST['noxcodes']);
        $noimages         = isset($_POST['noimages']);
        $nobreak          = isset($_POST['nobreak']);
        $indexheaderalign = $_POST['indexheaderalign'];
        $indexfooteralign = $_POST['indexfooteralign'];

        $xoopsDB->query(
            "update " . $xoopsDB->prefix("wfdownloads_indexpage") . " set indexheading='$indexheading', indexheader='$indexheader', indexfooter='$indexfooter', indeximage='$indeximage', indexheaderalign='$indexheaderalign', indexfooteralign='$indexfooteralign', nohtml='"
            . intval($nohtml) . "', nosmiley='" . intval($nosmiley) . "', noxcodes='" . intval($noxcodes) . "', noimages='" . intval($noimages)
            . "', nobreak='" . intval($nobreak) . "' "
        );
        redirect_header(WFDOWNLOADS_URL . '/admin/indexpage.php', 1, _AM_WFDOWNLOADS_IPAGE_UPDATED);
        exit();

        break;

    case "indexpage.form":
    default:
        include_once WFDOWNLOADS_ROOT_PATH . '/class/wfdownloads_lists.php';
        include XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        $result = $xoopsDB->query(
            "SELECT indeximage, indexheading, indexheader, indexfooter, nohtml, nosmiley, noxcodes, noimages, nobreak, indexheaderalign, indexfooteralign FROM "
            . $xoopsDB->prefix('wfdownloads_indexpage') . " "
        );
        list($indeximage, $indexheading, $indexheader, $indexfooter, $nohtml, $nosmiley, $noxcodes, $noimages, $nobreak, $indexheaderalign,
            $indexfooteralign)
            = $xoopsDB->fetchrow($result);

        wfdownloads_xoops_cp_header();
        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('indexpage.php');

        echo "<fieldset><legend>" . _AM_WFDOWNLOADS_IPAGE_INFORMATION . "</legend>\n";
        echo "<div>" . _AM_WFDOWNLOADS_MINDEX_PAGEINFOTXT . "</div>\n";
        echo "</fieldset>\n";

        $sform = new XoopsThemeForm(_AM_WFDOWNLOADS_IPAGE_MODIFY, "op", xoops_getenv('PHP_SELF'));
        $sform->addElement(new XoopsFormText(_AM_WFDOWNLOADS_IPAGE_CTITLE, 'indexheading', 60, 60, $indexheading), false);
        $graph_array       = WfsLists::getListTypeAsArray(XOOPS_ROOT_PATH . '/' . $wfdownloads->getConfig('mainimagedir'), $type = "images");
        $indeximage_select = new XoopsFormSelect('', 'indeximage', $indeximage);
        $indeximage_select->addOptionArray($graph_array);
        $indeximage_select->setExtra(
            "onchange='showImgSelected(\"image\", \"indeximage\", \"" . $wfdownloads->getConfig('mainimagedir') . "\", \"\", \"" . XOOPS_URL . "\")'"
        );
        $indeximage_tray = new XoopsFormElementTray(_AM_WFDOWNLOADS_IPAGE_CIMAGE, '&nbsp;');
        $indeximage_tray->addElement($indeximage_select);
        if (!empty($indeximage)) {
            $indeximage_tray->addElement(
                new XoopsFormLabel('', "<br /><br /><img src='" . XOOPS_URL . '/' . $wfdownloads->getConfig('mainimagedir') . '/' . $indeximage
                    . "' name='image' id='image' alt='' title='image' />")
            );
        } else {
            $indeximage_tray->addElement(
                new XoopsFormLabel('', "<br /><br /><img src='" . XOOPS_URL . "/uploads/blank.gif' name='image' id='image' alt='' title='image' />")
            );
        }
        $sform->addElement($indeximage_tray);

        $sform->addElement(new XoopsFormDhtmlTextArea(_AM_WFDOWNLOADS_IPAGE_CHEADING, 'indexheader', $indexheader, 15, 60));
        $headeralign_select = new XoopsFormSelect(_AM_WFDOWNLOADS_IPAGE_CHEADINGA, "indexheaderalign", $indexheaderalign);
        $headeralign_select->addOptionArray(
            array("left" => _AM_WFDOWNLOADS_IPAGE_CLEFT, "right" => _AM_WFDOWNLOADS_IPAGE_CRIGHT, "center" => _AM_WFDOWNLOADS_IPAGE_CCENTER)
        );
        $sform->addElement($headeralign_select);
        $sform->addElement(new XoopsFormDhtmlTextArea(_AM_WFDOWNLOADS_IPAGE_CFOOTER, 'indexfooter', $indexfooter, 15, 60));
        $footeralign_select = new XoopsFormSelect(_AM_WFDOWNLOADS_IPAGE_CFOOTERA, "indexfooteralign", $indexfooteralign);
        $footeralign_select->addOptionArray(
            array("left" => _AM_WFDOWNLOADS_IPAGE_CLEFT, "right" => _AM_WFDOWNLOADS_IPAGE_CRIGHT, "center" => _AM_WFDOWNLOADS_IPAGE_CCENTER)
        );
        $sform->addElement($footeralign_select);

        $options_tray = new XoopsFormElementTray(_AM_WFDOWNLOADS_TEXTOPTIONS, '<br />');

        $html_checkbox = new XoopsFormCheckBox('', 'nohtml', $nohtml);
        $html_checkbox->addOption(1, _AM_WFDOWNLOADS_ALLOWHTML);
        $options_tray->addElement($html_checkbox);

        $smiley_checkbox = new XoopsFormCheckBox('', 'nosmiley', $nosmiley);
        $smiley_checkbox->addOption(1, _AM_WFDOWNLOADS_ALLOWSMILEY);
        $options_tray->addElement($smiley_checkbox);

        $xcodes_checkbox = new XoopsFormCheckBox('', 'noxcodes', $noxcodes);
        $xcodes_checkbox->addOption(1, _AM_WFDOWNLOADS_ALLOWXCODE);
        $options_tray->addElement($xcodes_checkbox);

        $noimages_checkbox = new XoopsFormCheckBox('', 'noimages', $noimages);
        $noimages_checkbox->addOption(1, _AM_WFDOWNLOADS_ALLOWIMAGES);
        $options_tray->addElement($noimages_checkbox);

        $breaks_checkbox = new XoopsFormCheckBox('', 'nobreak', $nobreak);
        $breaks_checkbox->addOption(1, _AM_WFDOWNLOADS_ALLOWBREAK);
        $options_tray->addElement($breaks_checkbox);
        $sform->addElement($options_tray);

        $button_tray = new XoopsFormElementTray('', '');
        $hidden      = new XoopsFormHidden('op', 'indexpage.save');
        $button_tray->addElement($hidden);
        $button_tray->addElement(new XoopsFormButton('', 'post', _AM_WFDOWNLOADS_BSAVE, 'submit'));
        $sform->addElement($button_tray);
        $sform->display();
        break;
}
include 'admin_footer.php';
