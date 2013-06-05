<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @copyright  Marko Cupic 2012
 * @author     Marko Cupic, Oberkirch, Switzerland ->  mailto: m.cupic@gmx.ch
 * @package    be_email
 * @license    GNU/LGPL
 */
 
 
// Backend modules
$GLOBALS['BE_MOD']['Email']['tl_be_email'] = array
       (
              'icon' => 'system/modules/be_email/assets/mail.png',
              'tables' => array('tl_be_email'),
       );


if (TL_MODE == 'BE' && $_GET['do'] == 'tl_be_email')
{
       //CSS for the frontend-output
       $GLOBALS['TL_CSS'][] = 'system/modules/be_email/assets/be_email.css';
       if ($_GET['act'] == 'edit')
       {
              // Add the js
              $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/be_email/assets/be_email.js';
              // Auto growing textareas by http://davidwalsh.name/flext-textrea
              $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/be_email/assets/flext-master/flext.js';
       }
       
       // Hook - Add the fileupload-field
       if ($_GET['mode'] != 'addAddresses')
       {
              $GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('tl_be_email', 'parseBackendTemplateHook');
       }
}


      
