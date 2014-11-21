<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    Invites Invites
 * @ingroup     DolphinModules
 *
 * @{
 */

bx_import('BxDolModuleTemplate');

class BxInvTemplate extends BxDolModuleTemplate
{
    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);
    }

    public function getBlockInvite($iAccountId, $iProfileId)
    {
    	$iInvites = $this->_oConfig->getCountPerUser();
        $iInvited = $this->_oDb->getInvites(array('type' => 'count_by_account', 'value' => $iAccountId));

    	bx_import('BxDolPermalinks');
        $sUrl = BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink($this->_oConfig->CNF['URL_INVITE']);

        $this->addCss(array('main.css'));
    	return $this->parseHtmlByName('block_invite.html', array(
    		'style_prefix' => $this->_oConfig->getPrefix('style'),
    		'text' => _t('_bx_invites_txt_invite_block_text', $iInvites - $iInvited),
    		'url' => $sUrl
    	));
    }

    public function getBlockRequest()
    {
    	bx_import('BxDolPermalinks');
        $sUrl = BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink($this->_oConfig->CNF['URL_REQUEST']);

    	$this->addCss(array('main.css'));
    	return $this->parseHtmlByName('block_request.html', array(
    		'style_prefix' => $this->_oConfig->getPrefix('style'),
    		'text' => _t('_bx_invites_txt_request_block_text'),
    		'bx_if:show_button_request' => array(
    			'condition' => $this->_oConfig->isRequestInvite(),
    			'content' => array(
    				'url' => $sUrl
    			)
    		)
    	));
    }
}

/** @} */
