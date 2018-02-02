<?php
require_once('include/ListView/ListViewSmarty.php');
require_once('modules/AOS_PDF_Templates/formLetter.php');


class ContactsListViewSmarty extends ListViewSmarty {

	function __construct(){

		parent::__construct();
		$this->targetList = true;

	}

    /**
     * @deprecated deprecated since version 7.6, PHP4 Style Constructors are deprecated and will be remove in 7.8, please update your code, use __construct instead
     */
    function ContactsListViewSmarty(){
        $deprecatedMessage = 'PHP4 Style Constructors are deprecated and will be remove in 7.8, please update your code';
        if(isset($GLOBALS['log'])) {
            $GLOBALS['log']->deprecated($deprecatedMessage);
        }
        else {
            trigger_error($deprecatedMessage, E_USER_DEPRECATED);
        }
        self::__construct();
    }


	function process($file, $data, $htmlVar) {
		parent::process($file, $data, $htmlVar);

        	if(!ACLController::checkAccess($this->seed->module_dir,'export',true) || !$this->export) {
			$this->ss->assign('exportLink', $this->buildExportLink());
		}
	}

	function buildExportLink($id = 'export_link'){
		global $app_strings;
		global $sugar_config;

		$script = "";
		if(ACLController::checkAccess($this->seed->module_dir,'export',true)) {
			if($this->export) {
				$script = parent::buildExportLink($id);
			}
		}

		return formLetter::LVSmarty().$script;
	}

}

?>
