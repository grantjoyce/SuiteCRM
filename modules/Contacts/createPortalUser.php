<?php
/**
 *
 * @package Advanced OpenPortal
 * @copyright SalesAgility Ltd http://www.salesagility.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with this program; if not, see http://www.gnu.org/licenses
 * or write to the Free Software Foundation,Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA 02110-1301  USA
 *
 * @author SalesAgility Ltd <support@salesagility.com>
 */
if(!defined('sugarEntry'))define('sugarEntry', true);


include_once 'modules/Contacts/AOPContactUtils.php';
require_once 'modules/AOP_Case_Updates/util.php';

function url_exists($url)
{
    if (!$fp = curl_init($url)) return false;

    return true;
}

if (!isAOPEnabled()) {
    return;
}
global $sugar_config, $mod_strings;

require_once('modules/Contacts/Contact.php');

$bean = new Contact();
$bean->retrieve($_REQUEST['record']);

try {
    if (array_key_exists("aop", $sugar_config) && array_key_exists("joomla_urls", $sugar_config['aop'])) {
        $portalURLs = array_unique($sugar_config['aop']['joomla_urls']);
        // create only in the selected joomla portal...
        if (!isset($_REQUEST['selected_portal_url']) || !$_REQUEST['selected_portal_url']) {
            SugarApplication::appendErrorMessage($mod_strings['LBL_ERROR_NO_PORTAL_SELECTED']);
        } else {
            foreach ($portalURLs as $portalURL) {
                if ($portalURL) {
                    if ($_REQUEST['selected_portal_url'] == $portalURL) {

                        $jaList = BeanFactory::getBean('JAccount')->get_full_list('', "contact_id = '{$bean->id}' AND portal_url = '{$_REQUEST['selected_portal_url']}'", false, 1);
                        if (isset($jaList[0])) {
                            $ja = $jaList[0];
                            $ja->mark_undeleted($ja->id);
                            // todo: test somehow the marking as undeleted was success? ($ja->deleted = 0? or retrieve again..?)
                            //$ja->deleted = 0;
                        } else {
                            $ja = BeanFactory::getBean('JAccount');
                        }

                        $ja->name = $bean->name;
                        $ja->email1 = $bean->email1;
                        $ja->contact_id = $bean->id;
                        $ja->portal_url = $_REQUEST['selected_portal_url'];
                        $ja->deleted = 0;
                        $ja->save();

                        if ($multiplePortalSupportAvailable = AOPContactUtils::isMultiplePortalSupportAvailable($portalURL)) {
                            $url = $portalURL . '/index.php?m=JAccount&option=com_advancedopenportal&task=create&sug=' . $ja->id . '::' . $_REQUEST['record'];
                        } else {
                            if (count($portalURLs) > 1) {
                                $msg = $mod_strings['LBL_PLEASE_UPDATE_DEPRECATED_PORTAL_ERROR'] . " ($portalURL)";
                                SugarApplication::appendErrorMessage($msg);
                                SugarApplication::redirect("index.php?module=Contacts&action=DetailView&record=" . $_REQUEST['record']);

                                return;
                            }
                            $url = $portalURL . '/index.php?m=JAccount&option=com_advancedopenportal&task=create&sug=' . $_REQUEST['record'];
                            $msg = $mod_strings['LBL_PLEASE_UPDATE_DEPRECATED_PORTAL_WARNING'] . " ($portalURL)";
                            SugarApplication::appendErrorMessage($msg);
                        }

                        $wbsv = file_get_contents($url);
    $res = json_decode($wbsv);
                        if (json_last_error() != JSON_ERROR_NONE) {
                            $json_msg = json_last_error();
                            if ($json_msg) {
                                $json_msg = " (json parser error: $json_msg)";
                            }
                            throw new Exception("Incorrect response from Joomla Portal Component $json_msg: " . $wbsv);
                        }

                        // append portal to messages because since multiple portal possible the url doesn't make sense

                        if(!is_object($res)) {
                            trigger_error("Results should be an object, " . gettype($res) . " given");
                        }

                        if (!isset($res->success) || !$res->success) {
                            $ja->mark_deleted($ja->id);
                            $msg = $res->error ? $res->error : ($mod_strings['LBL_CREATE_PORTAL_USER_FAILED'] . " ($portalURL)");
        SugarApplication::appendErrorMessage($msg);
    } else {
                            SugarApplication::appendErrorMessage($mod_strings['LBL_CREATE_PORTAL_USER_SUCCESS'] . " ($portalURL)");
                        }
                    }
                }
            }
    }
} else {
    SugarApplication::appendErrorMessage($mod_strings['LBL_NO_JOOMLA_URL']);
    }
} catch(AOPContactUtilsException $e) {
    $eCode = $e->getCode();
    switch($eCode) {
        case AOPContactUtilsException::UNABLE_READ_PORTAL_VERSION :
            SugarApplication::appendErrorMessage($mod_strings['LBL_UNABLE_READ_PORTAL_VERSION'] . " ($portalURL)");
            break;

        default:
            throw $e;
    }
} catch(JAccountInvalidUserDataException $e) {
    SugarApplication::appendErrorMessage($mod_strings['LBL_INVALID_USER_DATA']);
}

SugarApplication::redirect("index.php?module=Contacts&action=DetailView&record=".$_REQUEST['record']);
