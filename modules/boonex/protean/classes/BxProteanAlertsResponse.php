<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Protean Protean template
 * @ingroup     UnaModules
 *
 * @{
 */

class BxProteanAlertsResponse extends BxBaseModTemplateAlertsResponse
{
    function __construct()
    {
        $this->_sModule = 'bx_protean';

        parent::__construct();
    }
}

/** @} */
