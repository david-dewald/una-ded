<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaManageCmd UNA Manage command line script
 * @{
 */

$GLOBALS['bx_profiler_disable'] = true;
define('BX_DOL_CRON_EXECUTE', '1');

/**
 * UNA command line interface
 */
class BxDolManageCmd
{
    protected $_sCmd;
    protected $_sCmdOptions;
    protected $_sPathToUna;
    protected $_aSiteConfig;
    protected $_isQuiet = false;
    protected $_aReturnCodes = array(
        'success' => array ('code' => 0, 'msg' => 'Success.'),
        'una not found' => array ('code' => 1, 'msg' => 'UNA wasn\'t found in the specified path: '),
        'unknown cmd' => array ('code' => 2, 'msg' => 'Unknown command.'),
        'db connect failed' => array ('code' => 3, 'msg' => 'Database connection failed: '),
        'system update failed' => array ('code' => 4, 'msg' => 'System update failed: '),
        'module operation failed' => array ('code' => 5, 'msg' => 'Module operation failed: '),
        'error' => array ('code' => 9, 'msg' => 'Error occured.'),
    );

    public function __construct()
    {
        $this->_aSiteConfig = array (
            'db_host' => '',
            'db_port' => '',
            'db_sock' => '',
            'db_name' => '',
            'db_user' => '',
            'db_password' => '',
        );
    }

    public function main()
    {
        // set neccessary options

        $a = getopt('hqc:u:p:o:', $this->getOptions());

        if (isset($a['h']))
            $this->finish($this->_aReturnCodes['success']['code'], $this->getHelp());

        if (isset($a['q']))
            $this->_isQuiet = true;

        if (isset($a['u']))
            $this->_sPathToUna = $a['u'];

        if (isset($a['c']))
            $this->_sCmd = $a['c'];

        if (isset($a['o']))
            $this->_sCmdOptions = $a['o'];

        $this->_aSiteConfig = array_merge($this->_aSiteConfig, $a);

        // initialize environment

        $this->init();

        // run command
        $sMethod = $this->_sCmd ? 'cmd' . bx_gen_method_name($this->_sCmd) : 'cmdDefault';
        if (!method_exists($this, $sMethod))
            $this->finish($this->_aReturnCodes['unknown cmd']['code'], $this->_aReturnCodes['unknown cmd']['msg']);

        $this->$sMethod();

        $this->finish($this->_aReturnCodes['success']['code'], $this->_aReturnCodes['success']['msg']);
    }

    protected function getOptions()
    {
        $a = array ();
        foreach ($this->_aSiteConfig as $sKey => $sValue)
            if ('site_config' != $sKey)
                $a[] = "$sKey::";
        return $a;
    }

    protected function getHelp()
    {
        $s = "Usage: php manage.php [params]\n";

        $s .= str_pad("\t -h", 35) . "Print this help\n";
        $s .= str_pad("\t -q", 35) . "Quiet\n";
        $s .= str_pad("\t -u", 35) . "Path to UNA\n";
        $s .= str_pad("\t -c", 35) . "Command to run:\n";
        $s .= str_pad("\t", 39) . "- update - options:\n";
        $s .= str_pad("\t", 43) . "'ignore_version_check' to ignore version comparison in DB and files\n";
        $s .= str_pad("\t", 43) . "'skip_files_op' skip files oprations, such copying and deleting\n";
        $s .= str_pad("\t", 39) . "- check_update - no options\n";
        $s .= str_pad("\t", 39) . "- update_modules - options:\n";
        $s .= str_pad("\t", 43) . "comma separated list of modules paths (ex: 'boonex/ads,boonex/wiki')\n";
        $s .= str_pad("\t", 39) . "- check_modules_updates - no options\n";
        $s .= str_pad("\t", 39) . "- install_modules - options:\n";
        $s .= str_pad("\t", 43) . "comma separated list of modules paths (ex: 'boonex/ads,boonex/wiki')\n";
        $s .= str_pad("\t", 39) . "- uninstall_modules - options:\n";
        $s .= str_pad("\t", 43) . "comma separated list of modules paths (ex: 'boonex/ads,boonex/wiki')\n";
        $s .= str_pad("\t", 39) . "- disable_modules - options:\n";
        $s .= str_pad("\t", 43) . "comma separated list of modules paths (ex: 'boonex/ads,boonex/wiki')\n";
        $s .= str_pad("\t", 39) . "- enable_modules - options:\n";
        $s .= str_pad("\t", 43) . "comma separated list of modules paths (ex: 'boonex/ads,boonex/wiki')\n";
        $s .= str_pad("\t -o", 35) . "Command options\n";

        foreach ($this->_aSiteConfig as $sKey => $sVal)
            if ('site_config' != $sKey)
                $s .= str_pad("\t --{$sKey}=<value>", 35) . "Database connection params\n";

        $s .= "\nIf DB params aren't specified then UNA DB params from header.inc.php are used.\n\n";

        $s .= "Examples:\n";
        $s .= "\tCheck if update is available:\n";
        $s .= "\tphp ./manage.php -u ../../unafolder --db_name=unadb --db_user=root --db_host=mysql --db_password=root -c check_update\n\n";
        $s .= "\tCheck if updates for modules are available:\n";
        $s .= "\tphp ./manage.php -u ../../unafolder --db_name=unadb --db_user=root --db_host=mysql --db_password=root -c check_modules_updates\n\n";
        $s .= "\tInstall Ads and Albums modules:\n";
        $s .= "\tphp ./manage.php -u ../../unafolder --db_name=unadb --db_user=root --db_host=mysql --db_password=root -c install_modules -o boonex/ads,boonex/albums\n\n";

        $s .= "Return codes:\n";
        foreach ($this->_aReturnCodes as $r) {
            $s .= str_pad("\t {$r['code']}", 5) . $r['msg'] . (mb_substr(trim($r['msg']), -1) == ':' ? 'message' : '') . "\n";
        }

        return $s;
    }

    protected function finish($iCode, $sMsg)
    {
        if (!$this->_isQuiet)
            fwrite($iCode ? STDERR : STDOUT, $sMsg . "\n");

        exit($iCode);
    }

    protected function init()
    {
        $this->_sPathToUna = trim($this->_sPathToUna);

        if ('.' == $this->_sPathToUna)
            $this->_sPathToUna = '';
        
        if (!$this->_sPathToUna || '/' !== $this->_sPathToUna[0]) {
            $aPathInfo = pathinfo(__FILE__);
            $this->_sPathToUna = $aPathInfo['dirname'] . '/' . $this->_sPathToUna;
        }

        $this->_sPathToUna = rtrim($this->_sPathToUna, '/');

        // include header.inc.php
        $sHeaderPath = $this->_sPathToUna . '/inc/header.inc.php';

        if (!file_exists($sHeaderPath))
            $this->finish($this->_aReturnCodes['una not found']['code'], $this->_aReturnCodes['una not found']['msg'] . $sHeaderPath);

        require_once($sHeaderPath);

        // db connection
        if (!empty($this->_aSiteConfig['db_name'])) {
            BxDolDb::getInstance()->cacheParamsClear();
            BxDolDb::getInstance()->disconnect();
            unset($GLOBALS['bxDolClasses']['BxDolDb']);
            $sErr = null;
            try {
                $oDb = BxDolDb::getInstanceWithConf(array(
                    'host' => $this->_aSiteConfig['db_host'],
                    'port' => $this->_aSiteConfig['db_port'],
                    'sock' => $this->_aSiteConfig['db_sock'],
                    'name' => $this->_aSiteConfig['db_name'],
                    'user' => $this->_aSiteConfig['db_user'],
                    'pwd' => $this->_aSiteConfig['db_password'],
                ), $sErr);
            } catch (Exception $e) {
                $this->finish($this->_aReturnCodes['db connect failed']['code'], $this->_aReturnCodes['db connect failed']['msg'] . $e->getMessage());
            }
        }
    }

    function cmdCheckUpdate()
    {
        $oUpgrader = bx_instance('BxDolUpgrader');
        $aUpdateInfo = $oUpgrader->getVersionUpdateInfo();
        if (!isset($aUpdateInfo['patch']))
            $this->finish($this->_aReturnCodes['success']['code'], 'No system update available');

        $s = str_pad(bx_get_ver(), 12) . str_pad($aUpdateInfo['patch']['ver'], 12) . $aUpdateInfo['latest_version']; 
        $this->finish($this->_aReturnCodes['success']['code'], $s);
    }

    function cmdUpdate()
    {
        $aOptions = $this->_parseOptions($this->_sCmdOptions);
        $oCronDb = BxDolCronQuery::getInstance();
        $aCronJobs = $oCronDb->getTransientJobs();

        if (!isset($aCronJobs['sys_perform_upgrade'])) {
            $oUpgrader = bx_instance('BxDolUpgrader');
            $aUpdateInfo = $oUpgrader->getVersionUpdateInfo();

            $bUpgrade = $oUpgrader->isUpgradeAvailable($aUpdateInfo);
            if (!$bUpgrade)
                $this->finish($this->_aReturnCodes['system update failed']['code'], $this->_aReturnCodes['system update failed']['msg'] . 'you have up to date version');

            if(!$oUpgrader->prepare(false, in_array('ignore_version_check', $aOptions)))
                $this->finish($this->_aReturnCodes['system update failed']['code'], $this->_aReturnCodes['system update failed']['msg'] . $oUpgrader->getError());
        }

        $aCronJobs = $oCronDb->getTransientJobs();
        if (empty($aCronJobs['sys_perform_upgrade']))
            $this->finish($this->_aReturnCodes['system update failed']['code'], $this->_aReturnCodes['system update failed']['msg'] . 'no cron job with patch path was found');

        $sPatchDir = BX_DIRECTORY_PATH_ROOT . $aCronJobs['sys_perform_upgrade']['file'];
        $sPatchDir = str_replace('BxDolUpgradeCron.php', '', $sPatchDir);
        define ('BX_UPGRADE_DIR_UPGRADES', $sPatchDir . 'files/');

        require_once($sPatchDir . 'classes/BxDolUpgradeController.php');
        require_once($sPatchDir . 'classes/BxDolUpgradeUtil.php');
        require_once($sPatchDir . 'classes/BxDolUpgradeDb.php');

        $oController = new BxDolUpgradeController();
        if ($oController->setMaintenanceMode(true)) {
            $sFolder = $oController->getAvailableUpgrade();
            if ($sFolder && $oController->runUpgrade($sFolder, in_array('ignore_version_check', $aOptions), in_array('skip_files_op', $aOptions))) {
                setParam('sys_revision', getParam('sys_revision') + 1);
                @bx_rrmdir($sUpgradeDir);
            }
            $oController->setMaintenanceMode(false);
        }

        $oCronDb->deleteTransientJobs();

        if ($sErrorMsg = $oController->getErrorMsg()) {
            $this->finish($this->_aReturnCodes['system update failed']['code'], $this->_aReturnCodes['system update failed']['msg'] . $sErrorMsg);
        }
    }

    function cmdCheckModulesUpdates()
    {
        $a = BxDolStudioInstallerUtils::getInstance()->checkUpdates();
        if (!$a)
            $this->finish($this->_aReturnCodes['success']['code'], 'No modules updates available');

        $s = '';
        foreach ($a as $r) {
            $s .= str_pad($r['name'], 20) . str_pad($r['file_version'], 12) . $r['file_version_to'] . "\n";
        }
        $this->finish($this->_aReturnCodes['success']['code'], trim($s));
    }

    function cmdUpdateModules()
    {
        $aModules = $this->_parseOptions($this->_sCmdOptions);
        var_dump($aModules);
    }

    function cmdInstallModules()
    {
        $this->_cmdModules('install', ['auto_enable' => true, 'html_response' => false]);
    }

    function cmdUninstallModules()
    {
        $this->_cmdModules('uninstall', ['html_response' => false]);
    }

    function cmdEnableModules()
    {
        $this->_cmdModules('enable', ['html_response' => false]);
    }

    function cmdDisableModules()
    {
        $this->_cmdModules('disable', ['html_response' => false]);
    }

    function _parseOptions($s) 
    {
        if (!$s)
            return [];
        $a = explode(',', trim($s));
        foreach ($a as $k => $v)
            $a[$k] = trim($v);
        return $a;
    }

    function _cmdModules($sOperation, $aOptions = [])
    {
        $aModules = $this->_parseOptions($this->_sCmdOptions);
        foreach ($aModules as $sModule) {
            $sModule = trim($sModule, '/') . '/';
            $a = BxDolStudioInstallerUtils::getInstance()->perform($sModule, $sOperation, array('auto_enable' => true, 'html_response' => false));
            if (!isset($a['code']) || $a['code'] !== BX_DOL_STUDIO_IU_RC_SUCCESS)
                $this->finish($this->_aReturnCodes['module operation failed']['code'], $this->_aReturnCodes['module operation failed']['msg'] . (!empty($a['message']) ? $a['message'] : 'Error occured.') . ' (' . $sOperation . ': ' . $sModule . ')');
        }
    }

}

$o = new BxDolManageCmd();
$o->main();

/** @} */
