<?
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\EventManager,
    Bitrix\Main\ModuleManager,
    Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

class awz_autounp extends CModule
{
	var $MODULE_ID = "awz.autounp";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;
    var $MODULE_GROUP_RIGHTS = "N";

    public function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__.'/version.php');

        $dirs = explode('/',dirname(__DIR__ . '../'));
        $this->MODULE_ID = array_pop($dirs);
        unset($dirs);

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("AWZ_AUTOUNP_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("AWZ_AUTOUNP_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = Loc::getMessage("AWZ_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("AWZ_PARTNER_URI");
		return true;
	}

    function DoInstall()
    {
        global $APPLICATION, $step;

        $this->InstallFiles();
        $this->InstallDB();
        $this->checkOldInstallTables();
        $this->InstallEvents();
        $this->createAgents();

        ModuleManager::RegisterModule($this->MODULE_ID);
        LocalRedirect('/bitrix/admin/settings.php?lang='.LANG.'&mid='.$this->MODULE_ID.'&mid_menu=1');

        return true;
    }

    function DoUninstall()
    {
        $this->deleteAgents();
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        ModuleManager::UnRegisterModule($this->MODULE_ID);
        return true;
        /*
        global $APPLICATION, $step;

        $step = intval($step);
        if($step < 2) {
            $APPLICATION->IncludeAdminFile(Loc::getMessage('AWZ_AUTOUNP_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'. $this->MODULE_ID .'/install/unstep.php');
        }
        elseif($step == 2) {
            if($_REQUEST['save'] != 'Y' && !isset($_REQUEST['save'])) {
                $this->UnInstallDB();
            }
            $this->UnInstallFiles();
            $this->UnInstallEvents();
            $this->deleteAgents();

            ModuleManager::UnRegisterModule($this->MODULE_ID);

            return true;
        }*/
    }

    function InstallDB()
    {
        return true;
    }

    function UnInstallDB()
    {
        return true;
    }

    function InstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandlerCompatible("main", "OnEndBufferContent",
            $this->MODULE_ID, '\Awz\AutoUnp\HandlersBx', 'OnEndBufferContent'
        );
        $eventManager->registerEventHandlerCompatible("main", "OnPageStart",
            $this->MODULE_ID, '\Awz\AutoUnp\HandlersBx', 'OnPageStart'
        );
        return true;
    }

    function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'main', 'OnEndBufferContent',
            $this->MODULE_ID, '\Awz\AutoUnp\HandlersBx', 'OnEndBufferContent'
        );
        $eventManager->unRegisterEventHandler(
            'main', 'OnPageStart',
            $this->MODULE_ID, '\Awz\AutoUnp\HandlersBx', 'OnPageStart'
        );
        return true;
    }

    function InstallFiles()
    {
        \CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID, true);
        \CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/css/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/css/".$this->MODULE_ID, true);
        return true;
    }

    function UnInstallFiles()
    {
        \DeleteDirFilesEx("/bitrix/js/".$this->MODULE_ID);
        \DeleteDirFilesEx("/bitrix/css/".$this->MODULE_ID);
        return true;
    }

    function createAgents() {
        return true;
    }

    function deleteAgents() {
        return true;
    }

    function checkOldInstallTables(){

        return true;

    }
}