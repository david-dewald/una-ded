<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Classes Classes
 * @ingroup     UnaModules
 *
 * @{
 */

/**
 * Create/Edit entry form
 */
class BxClssFormEntry extends BxBaseModTextFormEntry
{
    protected $_sGhostTemplateCover = 'form_ghost_template_cover.html';
    protected $_oProfileContext = null;
	
    public function __construct($aInfo, $oTemplate = false)
    {
        $this->MODULE = 'bx_classes';
        parent::__construct($aInfo, $oTemplate);

        $CNF = &$this->_oModule->_oConfig->CNF;

    	if(isset($CNF['FIELD_COVER']) && isset($this->aInputs[$CNF['FIELD_COVER']])) {
            if($this->_oModule->checkAllowedSetThumb() === CHECK_ACTION_RESULT_ALLOWED) {
                $this->aInputs[$CNF['FIELD_COVER']]['storage_object'] = $CNF['OBJECT_STORAGE'];
                $this->aInputs[$CNF['FIELD_COVER']]['uploaders'] = !empty($this->aInputs[$CNF['FIELD_COVER']]['value']) ? unserialize($this->aInputs[$CNF['FIELD_COVER']]['value']) : $CNF['OBJECT_UPLOADERS'];
                $this->aInputs[$CNF['FIELD_COVER']]['upload_buttons_titles'] = array(
                    'Simple' => _t('_bx_classes_form_entry_input_covers_uploader_simple_title'), 
                    'HTML5' => _t('_bx_classes_form_entry_input_covers_uploader_html5_title')
                );
                $this->aInputs[$CNF['FIELD_COVER']]['images_transcoder'] = $CNF['OBJECT_IMAGES_TRANSCODER_PREVIEW'];
                $this->aInputs[$CNF['FIELD_COVER']]['storage_private'] = 0;
                $this->aInputs[$CNF['FIELD_COVER']]['multiple'] = false;
                $this->aInputs[$CNF['FIELD_COVER']]['content_id'] = 0;
                $this->aInputs[$CNF['FIELD_COVER']]['ghost_template'] = '';
            }
            else
                unset($this->aInputs[$CNF['FIELD_COVER']]);
        }

        if(isset($CNF['FIELD_PHOTO']) && isset($this->aInputs[$CNF['FIELD_PHOTO']])) {
            $this->aInputs[$CNF['FIELD_PHOTO']]['storage_object'] = $CNF['OBJECT_STORAGE_PHOTOS'];
            $this->aInputs[$CNF['FIELD_PHOTO']]['uploaders'] = !empty($this->aInputs[$CNF['FIELD_PHOTO']]['value']) ? unserialize($this->aInputs[$CNF['FIELD_PHOTO']]['value']) : $CNF['OBJECT_UPLOADERS'];
            $this->aInputs[$CNF['FIELD_PHOTO']]['images_transcoder'] = $CNF['OBJECT_IMAGES_TRANSCODER_PREVIEW_PHOTOS'];
            $this->aInputs[$CNF['FIELD_PHOTO']]['storage_private'] = 0;
            $this->aInputs[$CNF['FIELD_PHOTO']]['multiple'] = true;
            $this->aInputs[$CNF['FIELD_PHOTO']]['content_id'] = 0;
            $this->aInputs[$CNF['FIELD_PHOTO']]['ghost_template'] = '';
            $this->aInputs[$CNF['FIELD_PHOTO']]['tr_attrs'] = array('class'=> 'bx-base-text-attachment-item');
        }

        if(isset($this->aInputs[$CNF['FIELD_VIDEO']])) {
            $this->aInputs[$CNF['FIELD_VIDEO']]['storage_object'] = $CNF['OBJECT_STORAGE_VIDEOS'];
            $this->aInputs[$CNF['FIELD_VIDEO']]['uploaders'] = !empty($this->aInputs[$CNF['FIELD_VIDEO']]['value']) ? unserialize($this->aInputs[$CNF['FIELD_VIDEO']]['value']) : $CNF['OBJECT_UPLOADERS'];
            $this->aInputs[$CNF['FIELD_VIDEO']]['images_transcoder'] = $CNF['OBJECT_VIDEOS_TRANSCODERS']['poster_preview'];
            $this->aInputs[$CNF['FIELD_VIDEO']]['storage_private'] = 0;
            $this->aInputs[$CNF['FIELD_VIDEO']]['multiple'] = true;
            $this->aInputs[$CNF['FIELD_VIDEO']]['content_id'] = 0;
            $this->aInputs[$CNF['FIELD_VIDEO']]['ghost_template'] = '';
            $this->aInputs[$CNF['FIELD_VIDEO']]['tr_attrs'] = array('class'=> 'bx-base-text-attachment-item');
        }

        if(isset($this->aInputs[$CNF['FIELD_SOUND']])) {
            $this->aInputs[$CNF['FIELD_SOUND']]['storage_object'] = $CNF['OBJECT_STORAGE_SOUNDS'];
            $this->aInputs[$CNF['FIELD_SOUND']]['uploaders'] = !empty($this->aInputs[$CNF['FIELD_SOUND']]['value']) ? unserialize($this->aInputs[$CNF['FIELD_SOUND']]['value']) : $CNF['OBJECT_UPLOADERS'];
            $this->aInputs[$CNF['FIELD_SOUND']]['images_transcoder'] = $CNF['OBJECT_IMAGES_TRANSCODER_PREVIEW_FILES'];
            $this->aInputs[$CNF['FIELD_SOUND']]['storage_private'] = 0;
            $this->aInputs[$CNF['FIELD_SOUND']]['multiple'] = true;
            $this->aInputs[$CNF['FIELD_SOUND']]['content_id'] = 0;
            $this->aInputs[$CNF['FIELD_SOUND']]['ghost_template'] = '';
            $this->aInputs[$CNF['FIELD_SOUND']]['tr_attrs'] = array('class'=> 'bx-base-text-attachment-item');
        }

        if (isset($CNF['FIELD_FILE']) && isset($this->aInputs[$CNF['FIELD_FILE']])) {
            $this->aInputs[$CNF['FIELD_FILE']]['storage_object'] = $CNF['OBJECT_STORAGE_FILES'];
            $this->aInputs[$CNF['FIELD_FILE']]['uploaders'] = !empty($this->aInputs[$CNF['FIELD_FILE']]['value']) ? unserialize($this->aInputs[$CNF['FIELD_FILE']]['value']) : $CNF['OBJECT_UPLOADERS'];
            $this->aInputs[$CNF['FIELD_FILE']]['images_transcoder'] = $CNF['OBJECT_IMAGES_TRANSCODER_PREVIEW_FILES'];
            $this->aInputs[$CNF['FIELD_FILE']]['storage_private'] = 0;
            $this->aInputs[$CNF['FIELD_FILE']]['multiple'] = true;
            $this->aInputs[$CNF['FIELD_FILE']]['content_id'] = 0;
            $this->aInputs[$CNF['FIELD_FILE']]['ghost_template'] = '';
            $this->aInputs[$CNF['FIELD_FILE']]['tr_attrs'] = array('class'=> 'bx-base-text-attachment-item');
        }

        if(isset($this->aInputs[$CNF['FIELD_POLL']])) {
            $this->aInputs[$CNF['FIELD_POLL']]['tr_attrs'] = array('class'=> 'bx-base-text-attachment-item');
        }
    }

    function initChecker ($aValues = array (), $aSpecificValues = array())
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        $bValues = $aValues && !empty($aValues['id']);
        $aContentInfo = $bValues ? $this->_oModule->_oDb->getContentInfoById($aValues['id']) : false;

        if($this->aParams['display'] == $CNF['OBJECT_FORM_ENTRY_DISPLAY_EDIT'] && isset($CNF['FIELD_PUBLISHED']) && isset($this->aInputs[$CNF['FIELD_PUBLISHED']]))
            if($bValues && in_array($aValues[$CNF['FIELD_STATUS']], array('active', 'hidden')))
                unset($this->aInputs[$CNF['FIELD_PUBLISHED']]);

        if (isset($CNF['FIELD_COVER']) && isset($this->aInputs[$CNF['FIELD_COVER']])) {
            if($bValues)
                $this->aInputs[$CNF['FIELD_COVER']]['content_id'] = $aValues['id'];

            $this->aInputs[$CNF['FIELD_COVER']]['ghost_template'] = $this->_oModule->_oTemplate->parseHtmlByName($this->_sGhostTemplateCover, $this->_getCoverGhostTmplVars($aContentInfo));
        }

        parent::initChecker ($aValues, $aSpecificValues);

        // try to get context from form POST
        $iProfileId = bx_get($CNF['FIELD_ALLOW_VIEW_TO']);

        // try to get context from the form saved value (in case of "edit" form)
        if ($iProfileId >= 0 && isset($this->aInputs[$CNF['FIELD_ALLOW_VIEW_TO']]) && $this->aInputs[$CNF['FIELD_ALLOW_VIEW_TO']]['value'] < 0)
            $iProfileId = $this->aInputs[$CNF['FIELD_ALLOW_VIEW_TO']]['value'];

        // try to get context from GET param (in case of "create" form)
        if ($iProfileId >= 0)
            $iProfileId = bx_get('profile_id');

        // try to get context from class id
        if (($iClassId = (int)bx_get('id')) && $aClass = $this->_oModule->_oDb->getContentInfoById($iClassId)) {
            $iProfileId = $aClass[$CNF['FIELD_ALLOW_VIEW_TO']];
        }

        $iProfileId = abs($iProfileId);
        if (isset($this->aInputs[$CNF['FIELD_ALLOW_VIEW_TO']])) {
            $this->aInputs[$CNF['FIELD_ALLOW_VIEW_TO']]['value'] = -$iProfileId;
            $this->aInputs[$CNF['FIELD_ALLOW_VIEW_TO']]['type'] = 'hidden';
        }

        if ($iProfileId)
            $this->_oProfileContext = BxDolProfile::getInstance($iProfileId);

        if (isset($this->aInputs[$CNF['FIELD_MODULE']]) && $this->_oProfileContext) {
            $this->aInputs[$CNF['FIELD_MODULE']]['values'] = $this->_oModule->_oDb->getEntriesModulesByContext($this->_oProfileContext->id(), true);
        }
    }

    public function insert ($aValsToAdd = array(), $isIgnore = false)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        if(isset($CNF['FIELD_ADDED']) && empty($aValsToAdd[$CNF['FIELD_ADDED']])) {
            $iAdded = 0;
            if(isset($this->aInputs[$CNF['FIELD_ADDED']]))
                $iAdded = $this->getCleanValue($CNF['FIELD_ADDED']);
            
            if(empty($iAdded))
                 $iAdded = time();

            $aValsToAdd[$CNF['FIELD_ADDED']] = $iAdded;
        }

        if(empty($aValsToAdd[$CNF['FIELD_PUBLISHED']])) {
            $iPublished = 0;
            if(isset($this->aInputs[$CNF['FIELD_PUBLISHED']]))
                $iPublished = $this->getCleanValue($CNF['FIELD_PUBLISHED']);
                
             if(empty($iPublished))
                 $iPublished = time();

             $aValsToAdd[$CNF['FIELD_PUBLISHED']] = $iPublished;
        }

        $aValsToAdd[$CNF['FIELD_STATUS']] = $aValsToAdd[$CNF['FIELD_PUBLISHED']] > $aValsToAdd[$CNF['FIELD_ADDED']] ? 'awaiting' : 'active';

        $aValsToAdd['order'] = $this->_oModule->_oDb->getClassMaxOrder(abs($this->getCleanValue($CNF['FIELD_ALLOW_VIEW_TO'])), $this->getCleanValue($CNF['FIELD_MODULE']));

        $iContentId = parent::insert ($aValsToAdd, $isIgnore);
        if(!empty($iContentId)){
            $this->processFiles($CNF['FIELD_COVER'], $iContentId, true);
        }
        return $iContentId;
    }

    function update ($iContentId, $aValsToAdd = array(), &$aTrackTextFieldsChanges = null)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        if(empty($aValsToAdd[$CNF['FIELD_PUBLISHED']]) && isset($this->aInputs[$CNF['FIELD_PUBLISHED']])) {
            $iPublished = $this->getCleanValue($CNF['FIELD_PUBLISHED']);
            if(empty($iPublished))
                $iPublished = time();

            $aValsToAdd[$CNF['FIELD_PUBLISHED']] = $iPublished;
        }
        
        $iResult = parent::update ($iContentId, $aValsToAdd, $aTrackTextFieldsChanges);
        $this->processFiles($CNF['FIELD_COVER'], $iContentId, false);   
        return $iResult;
    }

    protected function _getCoverGhostTmplVars($aContentInfo = array())
    {
    	$CNF = &$this->_oModule->_oConfig->CNF;

    	return array (
            'name' => $this->aInputs[$CNF['FIELD_COVER']]['name'],
            'content_id' => $this->aInputs[$CNF['FIELD_COVER']]['content_id'],
            'editor_id' => isset($CNF['FIELD_TEXT_ID']) ? $CNF['FIELD_TEXT_ID'] : '',
            'thumb_id' => isset($CNF['FIELD_THUMB']) && isset($aContentInfo[$CNF['FIELD_THUMB']]) ? $aContentInfo[$CNF['FIELD_THUMB']] : 0,
            'name_thumb' => isset($CNF['FIELD_THUMB']) ? $CNF['FIELD_THUMB'] : ''
        );
    }

    protected function _getPhotoGhostTmplVars($aContentInfo = array())
    {
    	$CNF = &$this->_oModule->_oConfig->CNF;

    	return array (
            'name' => $this->aInputs[$CNF['FIELD_PHOTO']]['name'],
            'content_id' => (int)$this->aInputs[$CNF['FIELD_PHOTO']]['content_id'],
            'editor_id' => isset($CNF['FIELD_TEXT_ID']) ? $CNF['FIELD_TEXT_ID'] : '',
            'bx_if:set_thumb' => [
				'condition' => false,
				'content' => []
			],
    	);
    }

    function getCode($bDynamicMode = false)
    {
        if (!$this->_bViewMode) {
            if (!$this->_oProfileContext)
                return MsgBox(_t('_bx_classes_txt_err_cant_add_class_without_context'));

            if (isset($this->aInputs['module_id']) && !$this->aInputs['module_id']['values'])
                return MsgBox(_t('_bx_classes_txt_err_cant_add_class_without_modules'));

            if (!isAdmin() && !$this->_oModule->serviceIsCourseAdmin($this->_oProfileContext->id()))
                return MsgBox(_t('_sys_txt_access_denied'));
        }

        return parent::getCode($bDynamicMode);
    }

    protected function genCustomViewRowValueCompletedWhen($aInput)
    {
        unset($aInput['values_list_name']);
        return $aInput['value'] ? '<div class="bx-classes-field-view-completed-when">' . $this->genViewRowValueForSelect($aInput) . '</div>' : '';
    }

    protected function genCustomViewRowValueModuleId($aInput)
    {
        return $aInput['value'] ? '<div class="bx-classes-field-view-module">' . bx_process_output($this->_oModule->_oDb->getClassModuleTitleById ($aInput['value'])) . '</div>' : '';
    }

    protected function genCustomViewRowValueStartDate($aInput)
    {
        return $this->_genCustomViewRowValue($aInput, 'col-green1-dark');
    }

    protected function genCustomViewRowValueEndDate($aInput)
    {
        return $this->_genCustomViewRowValue($aInput, 'col-red1');
    }

    protected function _genCustomViewRowValue($aInput, $sClassAdd = '')
    {
        $sClass = "bx-classes-field-view-" . str_replace('_', '-', $aInput['name']) . ' ' . $sClassAdd;
        $sClass .= ' ' . $sClassAdd;

        $aInput['name'] .= '!';
        $s = parent::genViewRowValue($aInput);
        return $aInput['value'] ? '<div class="' . $sClass . '">' . $s . '</div>' : $s;
    }
}

/** @} */
